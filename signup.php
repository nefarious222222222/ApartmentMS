<?php
            require_once('database.php');

            function emailExists($conn, $email) {
                $stmt = $conn->prepare("SELECT * FROM account WHERE emailAdd = ?");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->num_rows > 0;
            }
            
            function usernameExists($conn, $username) {
                $stmt = $conn->prepare("SELECT * FROM account WHERE username = ?");
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->num_rows > 0;
            }

            function validateContactNumber($contact) {
                return (is_numeric($contact) && strlen($contact) === 11);
            }

            $errors = [];
            $errorDiv = '';
            
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $username = trim($_POST["username"]);
                $password = $_POST["password"];
                $confirmPass = $_POST["confirmPass"];
                $email = filter_var($_POST["emailAdd"], FILTER_SANITIZE_EMAIL);
                $contact = $_POST["contactNum"];
            
                if (empty($username) || empty($password) || empty($confirmPass) || empty($email) || empty($contact)) {
                    $errors[] = "All fields are required";
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email is not valid";
                }

                if (emailExists($conn, $email)) {
                    $errors[] = "Email already exists";
                }
                
                if (usernameExists($conn, $username)) {
                    $errors[] = "Username already exists";
                }
            
                if (strlen($password) < 8) {
                    $errors[] = "Password must be at least 8 characters long";
                }

                if ($password !== $confirmPass) {
                    $errors[] = "Password does not match";
                }

                if (!validateContactNumber($contact)) {
                    $errors[] = "Contact number is not valid";
                }

                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $errorDiv .= "<div class='alertError'><p>$error</p></div>";
                    }
                }
                else {
                    $username = mysqli_real_escape_string($conn, $_POST["username"]);
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $email = filter_var($_POST["emailAdd"], FILTER_SANITIZE_EMAIL);
                    $contact = $_POST["contactNum"];

                    $sql = "INSERT INTO account (username, password, emailAdd, contactNum) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ssss', $username, $password, $email, $contact);

                    if ($stmt->execute()) {
                        header("Location: signin.php");
                        exit();
                    } else {
                        $errors[] = "Account failed". $stmt->error;
                    }
                }
            }
            ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/sign.css" />
        <script src="public/js/redirect.js"></script>
        <style>
            .alertError {
                padding: 10px;
                margin: -15px 50px 25px 50px;
                border-radius: 5px;
                text-align: center;
                background-color: rgb(239, 151, 151);
            }

            p {
                margin: 10px;
                padding: 0px;
                color: rgb(62, 21, 21);
                font-size: 20px;
            }
        </style>
    </head>
    <body>
        <div class="signupBox">
            <div class="signIntro">
                <div class="signLogo">
                    <img class="drcmLogo" src="public/images/Logo.png" alt="DRCM Logo" onclick="goBack()"/>
                </div>

                <h1>
                    DRCM Apartment<br>Management System            
                </h1>
            </div>

            <Div class="typeSign">
                Sign Up
            </Div>

            <?php echo $errorDiv; ?>

            <form action="signup.php" method="post">
                <label>
                    Username:
                </label>
                <input type="text" placeholder="" name="username"/>

                <label>
                    Password:
                </label>
                <input type="password" placeholder="" name="password"/>
                    
                <label>
                    Confirm Password:
                </label>
                <input type="password" placeholder="" name="confirmPass"/>

                <label>
                    Email Address:
                </label>
                <input type="text" placeholder="" name="emailAdd"/>

                <label>
                    Contact Number:
                </label>
                <input type="number" placeholder="" class="no-spinner" name="contactNum"/>

                <input type="submit" value="Create Account"/>
            </form>

            <p class="questionAcc1">
                Already have an account? <a href="signin.php">Login here</a>
            </p>
        </div>
    </body>
</html>