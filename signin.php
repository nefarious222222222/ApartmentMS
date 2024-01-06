<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<?php
require_once('database.php');

function validateUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT password FROM account WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return password_verify($password, $row['password']);
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
        session_start();
        if (validateUser($conn, $username, $password)) {
            $_SESSION["user"] = $username; // Storing username instead of "yes"
            session_regenerate_id(true); // Regenerate session ID for security
            header("Location: index.php");
            die();
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
                <input type="text" placeholder=""  name="username"/>

                <label>
                    Password:                        
                </label>
                <input type="Password" placeholder=""  name="password"/>

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