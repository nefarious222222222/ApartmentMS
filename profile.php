<?php
session_start();
require_once('public/php/database.php');

if (isset($_SESSION["user"])) {
    $sql = "SELECT userID, username, contactNum, emailAdd FROM users";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userid = $row["userID"];
        $username = $row["username"];
        $contactNum = $row["contactNum"];
        $emailAdd = $row["emailAdd"];

        $profileCheckQuery = "SELECT * FROM profile WHERE userID = ?";
        $stmt = $conn->prepare($profileCheckQuery);
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $existingProfile = $stmt->get_result()->fetch_assoc();

        if ($existingProfile) {
            $fullname = $existingProfile["fullname"];
            $age = $existingProfile["age"];
            $contact = $existingProfile["contactNum"];
            $gender = $existingProfile["gender"];
            $dob = $existingProfile["dateOfBirth"];
            $email = $existingProfile["emailAdd"];
        }
        else {
            echo "<script>alert('You do not yet have a profile. Please first go to settings and change your profile.'); window.location='index.php';</script>";
        }
    }
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>    
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/profile.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
    </head>
    <body>
        <h1 class="title">
            Profile
        </h1>

        <div class="container">
            <div class="infoContainer">
                <div class="groupOne">
                    <div class="imgContainer">
                        <img src="public/images/profile.png">
                    </div>

                    <div class="groupTextOne">
                        <h4 class="type">
                            Name:
                        </h4>
                        <p class="info">
                            <?php echo isset($fullname) ? $fullname : ''; ?>
                        </p>

                        <h4 class="type">
                            Username:
                        </h4>
                        <p class="info">
                            <?php echo isset($username) ? $username : ''; ?>
                        </p>
                    </div>
                </div>
    
                   
                <div class="groupTwo">
                    <div class="groupTextTwo">
                        <h4 class="type">
                            Age:
                        </h4>
                        <p class="info">
                            <?php echo isset($age) ? $age : ''; ?>
                        </p>
        
                        
                        <h4 class="type">
                            Date of Birth:
                        </h4>
                        <p class="info">
                            <?php echo isset($dob) ? $dob : ''; ?>
                        </p>
                    </div>

                    <div class="groupTextOne">
                        <h4 class="type">
                            Contact Number:
                        </h4>
                        <p class="info">
                            <?php echo isset($contact) ? $contact : ''; ?>
                        </p>        
                        
                        <h4 class="type">
                            Email Address:
                        </h4>
                        <p class="info">
                            <?php echo isset($email) ? $email : ''; ?>
                        </p>
                    </div>
                </div>              
            </div>
        </div>
    </body>
</html>
</span>