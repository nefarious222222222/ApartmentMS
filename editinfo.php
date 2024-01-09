<?php
session_start();
require_once('public/php/database.php');

if (isset($_SESSION["user"])) {
    $sql = "SELECT userID, contactNum, emailAdd FROM users";
    $result = $conn->query($sql);

    $errors = [];
    $errorDiv = '';

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userid = $row["userID"];
        $contactNum = $row["contactNum"];
        $emailAdd = $row["emailAdd"];

        $fullname = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : '';
        $age = isset($_POST["age"]) ? trim($_POST["age"]) : '';
        $contact = isset($_POST["contactNum"]) ? trim($_POST["contactNum"]) : '';
        $gender = isset($_POST["gender"]) ? trim($_POST["gender"]) : '';
        $dob = isset($_POST["dateOfBirth"]) ? $_POST["dateOfBirth"] : '';
        $email = isset($_POST["emailAdd"]) ? filter_var($_POST["emailAdd"], FILTER_SANITIZE_EMAIL) : '';

        if (empty($fullname) || empty($age) || empty($contact) || empty($gender) || empty($dob) || empty($email)) {
            $errors[] = "All fields are required";
        }

        if (strtolower($gender) !== 'male' && strtolower($gender) !== 'female') {
            $errors[] = "Gender should be Male or Female";
        }

        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dob)) {
            $errors[] = "Date of birth should be in YYYY-MM-DD format";
        }

        if ($contact !== $contactNum) {
            $errors[] = "Contact number does not match your original contact number";
        }

        if ($email !== $emailAdd) {
            $errors[] = "Email does not match your original email";
        }

        if (empty($errors)) {
            $profileCheckQuery = "SELECT * FROM profile WHERE userID = ?";
            $stmt = $conn->prepare($profileCheckQuery);
            $stmt->bind_param('i', $userid);
            $stmt->execute();
            $existingProfile = $stmt->get_result()->fetch_assoc();

            if ($existingProfile) {
                $updateQuery = "UPDATE profile SET fullname=?, age=?, contactNum=?, gender=?, dateOfBirth=?, emailAdd=? WHERE userID=?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param('sissssi', $fullname, $age, $contact, $gender, $dob, $email, $userid);
                $stmt->execute();
            } else {
                $insertQuery = "INSERT INTO profile (fullname, age, contactNum, gender, dateOfBirth, emailAdd, userID) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param('sissssi', $fullname, $age, $contact, $gender, $dob, $email, $userid);
                $stmt->execute();
            }

            header("Location: index.php");
            exit();
        } else {
            foreach ($errors as $error) {
                $errorDiv .= "<div class='alertError'><p>$error</p></div>";
            }
        }
    } else {
        echo "0 results";
    }
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/editinfo.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
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
        <h1 class="title">
            Edit Profile
        </h1>

        <div class="editContainer">
            <?php echo $errorDiv; ?>

            <form method="post" action="editinfo.php">
                <div class="formContainer">
                    <div class="inputRow">
                        <label>
                            Full Name:                        
                        </label>
                        <input type="text" name="fullname"/>
    
                        <label>
                            Age:                        
                        </label>
                        <input type="number" class="no-spinner" name="age"/>
    
                        <label>
                            Contact Number:                        
                        </label>
                        <input type="number" class="no-spinner" name="contactNum" value="<?php echo isset($contactNum) ? htmlspecialchars($contactNum) : ''; ?>"/>
                    </div>
                    
                    <div class="inputRow">
                        <label>
                            Gender: Male or Female                       
                        </label>
                        <input type="text" name="gender"/>
    
                        <label>
                            Date of Birth:                        
                        </label>
                        <input type="text" name="dateOfBirth"/>
    
                        <label>
                            Email Address:                        
                        </label>
                        <input type="text" name="emailAdd" value="<?php echo isset($emailAdd) ? htmlspecialchars($emailAdd) : ''; ?>"/>
                    </div>
                </div>

                <input type="submit" value="Save Changes"/>     
            </form>
        </div>
    </body>
</html>
</span>