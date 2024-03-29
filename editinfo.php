<?php
session_start();
require_once('public/php/database.php');

$errors = [];
$errorDiv = '';

if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];

    $sql = "SELECT userID, contactNum, emailAdd FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userid = $row["userID"];
        $contactNum = $row["contactNum"];
        $emailAdd = $row["emailAdd"];

        if (isset($_POST["submit"])) {
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
        
            if ($contact !== $contactNum) {
                $errors[] = "Contact number does not match your original contact number";
            }
        
            if ($email !== $emailAdd) {
                $errors[] = "Email does not match your original email";
            }
        
            if (empty($errors)) {
                $profileCheckQuery = "SELECT * FROM profile WHERE userID = ?";
                $stmtProfile = $conn->prepare($profileCheckQuery);
                $stmtProfile->bind_param('i', $userid);
                $stmtProfile->execute();
                $existingProfile = $stmtProfile->get_result()->fetch_assoc();
        
                if ($existingProfile) {
                    $updateQuery = "UPDATE profile SET fullname=?, age=?, contactNum=?, gender=?, dateOfBirth=?, emailAdd=? WHERE userID=?";
                    $stmtUpdate = $conn->prepare($updateQuery);
                    $stmtUpdate->bind_param('sissssi', $fullname, $age, $contact, $gender, $dob, $email, $userid);
                    $stmtUpdate->execute();

                    echo "<script>alert('Profile updated successfully'); window.location='index.php';</script>";
                    exit();
                } else {
                    $insertQuery = "INSERT INTO profile (fullname, age, contactNum, gender, dateOfBirth, emailAdd, userID) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmtInsert = $conn->prepare($insertQuery);
                    $stmtInsert->bind_param('sissssi', $fullname, $age, $contact, $gender, $dob, $email, $userid);
                    $stmtInsert->execute();

                    echo "<script>alert('Profile created successfully'); window.location='index.php';</script>";
                    exit();
                }
            } else {
                foreach ($errors as $error) {
                    $errorDiv .= "<div class='alertError'><p>$error</p></div>";
                }
            }
        }
    } else {
        echo "0 results";
    }
}

if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];

    $sql = "SELECT userID, contactNum, emailAdd FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userid = $row["userID"];
        $contactNum = $row["contactNum"];
        $emailAdd = $row["emailAdd"];

        $profileCheckQuery = "SELECT * FROM profile WHERE userID = ?";
        $stmtProfile = $conn->prepare($profileCheckQuery);
        $stmtProfile->bind_param('i', $userid);
        $stmtProfile->execute();
        $existingProfile = $stmtProfile->get_result()->fetch_assoc();

        if ($existingProfile) {
            $fullname = $existingProfile["fullname"];
            $age = $existingProfile["age"];
            $gender = $existingProfile["gender"];
            $dob = $existingProfile["dateOfBirth"];
        }
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
        <Script src="public/js/redirect.js"></script>
        <script>
            function showEditConfirm() {
                document.getElementById("editConfirmation").style.display = "block"; 
            }

            function closeEditConfirm() {
                document.getElementById("editConfirmation").style.display = "none";
            }
        </script>
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
                        <input type="text" name="fullname" value="<?php echo isset($fullname) ? $fullname : ''; ?>"/>
    
                        <label>
                            Age:                        
                        </label>
                        <input type="number" class="no-spinner" name="age" value="<?php echo isset($age) ? $age : ''; ?>"/>
    
                        <label>
                            Contact Number:                        
                        </label>
                        <input type="number" class="no-spinner" name="contactNum" value="<?php echo isset($contactNum) ? htmlspecialchars($contactNum) : ''; ?>"/>
                    </div>
                    
                    <div class="inputRow">
                        <label>
                            Gender:                       
                        </label>
                        <select name="gender">
                            <option value="male" <?php echo (isset($gender) && $gender == 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (isset($gender) && $gender == 'female') ? 'selected' : ''; ?>>Female</option>
                        </select>
    
                        <label>
                            Date of Birth:                   
                        </label>
                        <input type="date" name="dateOfBirth" value="<?php echo isset($dob) ? $dob : ''; ?>"/>
    
                        <label>
                            Email Address:                        
                        </label>
                        <input type="text" name="emailAdd" value="<?php echo isset($emailAdd) ? htmlspecialchars($emailAdd) : ''; ?>"/>
                    </div>
                </div>
                
                <div class="editConfirmation" id="editConfirmation">
                    <div class="editContent">
                        <h2 class="editTitle">Edit Profile</h2>
                        <p class="editMessage">Do you want to save this profile?</p>

                        <div class="buttonContainer">
                            <input type="submit" name="submit" value="Yes"/>
                            <input type="button" onclick="closeEditConfirm()" value="No">
                        </div>
                    </div>
                </div>
                
                <button type="button" onclick="showEditConfirm()">Save</button>    
            </form>
        </div>
    </body>
</html>
</span>