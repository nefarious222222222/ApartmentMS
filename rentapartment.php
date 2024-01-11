<?php
session_start();
require_once('public/php/database.php');

$errors = [];
$errorDiv = '';
$apartNum = isset($_GET['apartNum']) ? $_GET['apartNum'] : null;

if (!$apartNum) {
    echo "<script>console.log('Apartment number invalid')</script>";
    exit();
}

$apartmentCheckQuery = "SELECT status FROM apartment WHERE apartmentID = ?";
$stmtApartment = $conn->prepare($apartmentCheckQuery);
$stmtApartment->bind_param('i', $apartNum);
$stmtApartment->execute();
$resultApartment = $stmtApartment->get_result();

if ($resultApartment && $resultApartment->num_rows > 0) {
    $rowApartment = $resultApartment->fetch_assoc();
    $apartmentStatus = $rowApartment["status"];

    if ($apartmentStatus == "available") {
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
        
                if (!$existingProfile) {
                    echo "<script>alert('Please set up your profile first!');  window.location='index.php';</script>";
                    exit();
                }
        
                $fullname = $existingProfile["fullname"];
                $dob = $existingProfile["dateOfBirth"];
            }
            
            if (isset($_POST["submit"])) {
                $fName = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : '';
                $dateOB = isset($_POST["dateOfBirth"]) ? $_POST["dateOfBirth"] : '';
                $contact = isset($_POST["contactNum"]) ? trim($_POST["contactNum"]) : '';  
                $email = isset($_POST["emailAdd"]) ? filter_var($_POST["emailAdd"], FILTER_SANITIZE_EMAIL) : '';
                $moveIn = isset($_POST["moveIn"]) ? $_POST["moveIn"] : '';
                $moveOut = isset($_POST["moveOut"]) ? $_POST["moveOut"] : '';  
                $validIdType = isset($_POST["validIdType"]) ? trim($_POST["validIdType"]) : '';
                $validIdNum = isset($_POST["validIdNum"]) ? trim($_POST["validIdNum"]) : '';
                $paymentMethod = isset($_POST["paymentMethod"]) ? trim($_POST["paymentMethod"]) : '';
            
                if (empty($fName) || empty($dateOB) || empty($contact) || empty($email) || empty($moveIn) || empty($moveOut) || empty($validIdType) || empty($validIdNum) || empty($paymentMethod)) {
                    $errors[] = "All fields are required";
                }
            
                if ($fName !== $fullname) {
                    $errors[] = "Fullname does not match your original fullname";
                }
        
                if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dateOB)) {
                    $errors[] = "Date of birth should be in YYYY-MM-DD format";
                }
            
                if ($dob !== $dateOB) {
                    $errors[] = "Date of birth does not match your original date of birth";
                }
            
                if ($contact !== $contactNum) {
                    $errors[] = "Contact number does not match your original contact number";
                }
            
                if ($email !== $emailAdd) {
                    $errors[] = "Email does not match your original email";
                }
        
                if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $moveIn)) {
                    $errors[] = "Move In should be in YYYY-MM-DD format";
                }
        
                if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $moveOut)) {
                    $errors[] = "Move Out should be in YYYY-MM-DD format";
                } else {
                    $moveInTimestamp = strtotime($moveIn);
                    $moveOutTimestamp = strtotime($moveOut);
                
                    $monthsDifference = floor(($moveOutTimestamp - $moveInTimestamp) / (30 * 24 * 60 * 60));
                
                    if ($monthsDifference < 3) {
                        $errors[] = "You need to rent the apartment for atleast 3 months";
                    }
                }
                
                if (strlen($validIdNum) != 10) {
                    $errors[] = "Valid Id should be exactly 10 digits";
                }
        
                if (strtolower($paymentMethod) !== 'gcash' && strtolower($paymentMethod) !== 'cash') {
                    $errors[] = "Payment method should be Gcash or Cash";
                }
        
                if (empty($errors)) {
                    $rentCheckQuery = "SELECT * FROM rent WHERE userID = ?";
                    $stmtRent = $conn->prepare($rentCheckQuery);
                    $stmtRent->bind_param('i', $userid);
                    $stmtRent->execute();
                    $existingRent = $stmtRent->get_result()->fetch_assoc();
            
                    if ($existingRent) {
                        echo "<script>alert('This transaction has already been created'); window.location='index.php';</script>";
                        exit();
                    } else {
                        $insertQuery = "INSERT INTO rent (fullname, dateOfBirth, contactNum, emailAdd, moveIn, moveOut, validIdType, validIdNum, paymentMethod, userID, apartmentID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($insertQuery);
                        $stmtInsert->bind_param('sssssssssii', $fName, $dateOB, $contact, $email, $moveIn, $moveOut, $validIdType, $validIdNum, $paymentMethod, $userid, $apartNum);
        
                        if ($stmtInsert->execute()) {
                            $updateApartmentStatusQuery = "UPDATE apartment SET status = 'unavailable' WHERE apartmentID = ?";
                            $stmtUpdateStatus = $conn->prepare($updateApartmentStatusQuery);
                            $stmtUpdateStatus->bind_param('i', $apartNum);
        
                            if ($stmtUpdateStatus->execute()) {
                                echo "<script>alert('Profile created successfully'); window.location='index.php';</script>";
                            } else {
                                echo "<script>alert('Failed to update apartment status');</script>";
                            }
                        } else {
                            echo "<script>alert('Failed to insert into rent table');</script>";
                        }
        
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
    } else {
        echo "<script>alert('Apartment is currently being rented'); windows.location='index.php';</script>";
    }
} else {
    echo "<script>alert('Apartment not found')</script>";
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/rentapartment.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
        <script src="public/js/redirect.js"></script>
        <script src="public/js/styling.js"></script>
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
         <h1 id="title" class="title">
            Rent Apartment
        </h1>

        <div class="cancelConfirmation" id="cancelConfirmation">
            <div class="cancelContent">
                <h2 class="cancelTitle">Cancel Confirmation</h2>
                <p class="cancelMessage">Do you want to cancel?</p>

                <div class="buttonContainer">
                    <button type="button" onclick="goBack()">Yes</button>
                    <button type="button" onclick="closeCancel()">No</button>
                </div>
            </div>
        </div>

        <div class="rentapartmentContainer">
            <div class="formContainer">
            <?php echo $errorDiv; ?>

                <form method="post" action="rentapartment.php?apartNum=<?php echo $apartNum; ?>">
                    <div class="inputRow">
                        <label>
                            Full Name:                        
                        </label>
                        <input type="text" name="fullname" value="<?php echo isset($fullname) ? $fullname : ''; ?>"/>
        
                        <label>
                            Date of Birth: YYYY-MM-DD                       
                        </label>
                        <input type="text" name="dateOfBirth" value="<?php echo isset($dob) ? $dob : ''; ?>"/>
    
                        <label>
                            Contact Number:                        
                        </label>
                        <input type="number" class="no-spinner" name="contactNum" value="<?php echo isset($contactNum) ? htmlspecialchars($contactNum) : ''; ?>"/>
    
                        <label>
                            Email Address:                        
                        </label>
                        <input type="text" name="emailAdd" value="<?php echo isset($emailAdd) ? htmlspecialchars($emailAdd) : ''; ?>"/>
                    </div>
                    
                    <div class="inputRow">
                        <label>
                            Move In (Date): YYYY-MM-DD                          
                        </label>
                        <input type="text" name="moveIn"/>
        
                        <label>
                            Move Out (Date): YYYY-MM-DD                          
                        </label>
                        <input type="text" name="moveOut"/>
    
                        <div class="validInfo">
                            <div class="groupOne">
                                <label>
                                    Valid ID Type:                        
                                </label>
                                <input class="validText" type="text" name="validIdType"/>
                            </div>
                                
                            <div class="groupTwo">
                                <label>
                                    Valid ID Number:                        
                                </label>
                                <input class="validText no-spinner" type="Number" name="validIdNum"/>
                            </div>   
                        </div>
    
                        <label>
                            Payment Method: Gcash or Cash                       
                        </label>
                        <input class="no-spinner" type="text" name="paymentMethod"/>
                    </div>

                    <div class="rentConfirmation" id="rentConfirmation">
                        <div class="rentContent">
                            <h2 class="rentTitle">Rent Confirmation</h2>
                            <p class="rentMessage">Are you sure you want to rent this apartment?</p>

                            <div class="buttonContainer">
                                <input class="confirmButton" type="submit" name="submit" value="Yes"/>

                                <input class="confirmButton" type="button" onclick="closeRent()" value="No"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="buttonGroup">
                <input class="mainButton" type="button" value="Cancel" onclick="showCancel()"/>

                <input class="mainButton" type="button" value="Rent" onclick="showRent()"/>
            </div> 
        </div>
    </body>
</html>
</span>