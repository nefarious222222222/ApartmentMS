<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

require_once('public/php/database.php');

function validateUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT userID, password FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            return $row['userID'];
        }
    }
    return false;
}

$errors = [];
$errorDiv = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            $errorDiv .= "<div class='alertError'><p>$error</p></div>";
        }
    } else {
        $userId = validateUser($conn, $username, $password);

        if ($userId !== false) {
            session_start();

            $userTypeSql = "SELECT userType FROM users WHERE username = ?";
            $userTypeStmt = $conn->prepare($userTypeSql);
            $userTypeStmt->bind_param('s', $username);
            $userTypeStmt->execute();
            $userTypeResult = $userTypeStmt->get_result();

            if ($userTypeResult && $userTypeResult->num_rows > 0) {
                $row = $userTypeResult->fetch_assoc();
                $userType = $row["userType"];
                
                if($userType == "admin"){
                    $_SESSION["user"] = $username;
                    $_SESSION["userID"] = $userId;
                    session_regenerate_id(true);
                    echo "<script>alert('Admin has logged in!'); window.location='index.php';</script>";
                    exit;
                } else {
                    $_SESSION["user"] = $username;
                    $_SESSION["userID"] = $userId;
                    echo "<script>alert('Account successfully signed in!'); window.location='index.php';</script>";
                    session_regenerate_id(true);
                    exit;
                }
            }
        } else {
            $errorDiv .= "<div class='alertError'><p>Invalid username or password</p></div>";
        }
    }
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
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
                font-size: 13px;
                transition: all 0.3s ease;
            }

            .alertError:hover {
                box-shadow: 0 0 7px 3px rgba(255, 183, 183, 0.704);
            }

            p {
                margin: 10px;
                padding: 0px;
                color: rgb(62, 21, 21);
            }
        </style>
    </head>
    <body>
        <div class="signinBox">
            <div class="signIntro">
                <div class="signLogo">
                    <img class="drcmLogo" src="public/images/logo.png" alt="DRCM Logo" onclick="goBack()"/>
                </div>

                <h1>
                    DRCM Apartment<br>Management System            
                </h1>
            </div>

            <Div class="typeSign">
                Sign In 
            </Div>                               
            
            <?php echo $errorDiv; ?>

            <form action="signin.php" method="post">
                <label>
                    Username:                        
                </label>
                <input type="text" name="username"/>

                <label>
                    Password:                        
                </label>
                <input type="Password" name="password"/>

                <input type="submit" value="Sign In" />

                <br></br>

                <div class="forgot">
                <a href="signup.html">Forgot Password? </a>
            </form>
        </div>

    <p class="questionAcc">
        Don't have an account? <a href="signup.php">Sign Up Here</a>
    </p>

    </body>
</html>
</span>