<?php
session_start();
require_once('public/php/database.php');

$errors = [];
$errorDiv = '';

if (isset($_SESSION["user"])) {
    $userId = $_SESSION["userID"];

    if (!empty($userId)) {
        if (isset($_POST["submit"])) {
            $apartmentID = isset($_POST["apartmentID"]) ? trim($_POST["apartmentID"]) : '';
            $about = isset($_POST["about"]) ? trim($_POST["about"]) : '';
            $request = isset($_POST["request"]) ? trim($_POST["request"]) : '';
        
            if (empty($apartmentID) || empty($about) || empty($request)) {
                $errors[] = "All fields are required";
            }
        
            if (empty($errors)) {
                $apartmentCheckQuery = "SELECT * FROM apartment WHERE apartmentID = ?";
                $stmtApartment = $conn->prepare($apartmentCheckQuery);
                $stmtApartment->bind_param('i', $apartmentID);
                $stmtApartment->execute();
                $apartmentExists = $stmtApartment->get_result()->fetch_assoc();
        
                if ($apartmentExists) {
                    $apaStatCheckQuery = "SELECT status FROM apartment WHERE apartmentID = ?";
                    $stmtApaStat = $conn->prepare($apaStatCheckQuery);
                    $stmtApaStat->bind_param('i', $apartmentID);
                    $stmtApaStat->execute();
                    $resultApaStat = $stmtApaStat->get_result();

                    echo "<script>Apartment Exists</script>";
                    if ($resultApaStat && $resultApaStat->num_rows > 0) {
                        $rowApaStat = $resultApaStat->fetch_assoc();
                        $apartmentStatus = $rowApaStat["status"];

                        if ($apartmentStatus == "unavailable") {
                            $insertQuery = "INSERT INTO maintenance (userID, apartmentID, about, request) VALUES (?, ?, ?, ?)";
                            $stmtInsert = $conn->prepare($insertQuery);
                            $stmtInsert->bind_param('iiss', $userId, $apartmentID, $about, $request);
                            $stmtInsert->execute();

                            echo "<script>alert('Maintenance request has been created'); window.location='index.php';</script>";
                            exit();
                        } else {
                            echo "<script>alert('Apartment is not being rented');</script>";
                        }
                    }
                } else {
                    echo "<script>alert('Apartment ID is invalid');</script>";
                    exit();
                }
            } else {
                foreach ($errors as $error) {
                    $errorDiv .= "<div class='alertError'><p>$error</p></div>";
                }
            }
        } else {
            echo "<script>console.log('Loaded successfully');;</script>";
        }
    } else {
        echo "0 results";
    }
}

$errors1 = [];
$errorDiv1 = '';

$maintenanceCheckQuery = "SELECT * FROM maintenance WHERE userID = ?";
$stmtMaintenance = $conn->prepare($maintenanceCheckQuery);
$stmtMaintenance->bind_param('i', $userId);
$stmtMaintenance->execute();
$resultMaintenance = $stmtMaintenance->get_result();

$maintenances = [];

if ($resultMaintenance && $resultMaintenance->num_rows > 0) {
    while ($row = $resultMaintenance->fetch_assoc()) {
        $maintenances[] = $row;
    }
} else {
    $errors1[] = "No maintenance request has been made by you.";

    foreach ($errors1 as $error) {
        $errorDiv1 .= "<div class='alertError'><p>$error</p></div>";
    }
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/maintenance.css" />
         <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
        <script src="public/js/redirect.js"></script>
        <script>
            function showMaintenanceConfirm() {
                document.getElementById("maintenanceConfirmation").style.display = "block"; 
            }

            function closeMaintenanceConfirm() {
                document.getElementById("maintenanceConfirmation").style.display = "none";
            }

            function toggleMaintenanceRequest() {
                document.getElementById("maintenanceRequest").style.display = "flex";
                document.getElementById("replyContainer").style.display = "none";
            }

            function toggleAdminReply() {
                document.getElementById("maintenanceRequest").style.display = "none";
                document.getElementById("replyContainer").style.display = "flex";
            }
        </script>
        <style>
           .alertError {
                padding: 10px;
                margin: 15px 50px 70px 50px;
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
            Maintenance Request          
        </h1>

        <div class="divContainer">
            <button class="toggleBtn" type="button" onclick="goBack()">Back</button>
            <button class="toggleBtn" type="button" onclick="toggleMaintenanceRequest()">Maintenance Request</button>
            <button class="toggleBtn" type="button" onclick="toggleAdminReply()">Admin's Reply</button>
        </div>

        <div class="changeContainer" id="maintenanceRequest">        
            <div class="formContainer">  
                <?php echo $errorDiv; ?>

                <form method="post" action="maintenance.php">
                    <label>
                        Apartment ID:                        
                    </label>
                    <input type="text" name="apartmentID" />

                    <label>
                        About:                        
                    </label>
                    <input type="text" name="about" />

                    <label>
                        Request:                        
                    </label>
                    <input type="text" name="request" />

                    <div class="maintenanceConfirmation" id="maintenanceConfirmation">
                        <div class="maintenanceContent">
                            <h2 class="maintenanceTitle">Maintenance Request</h2>
                            <p class="maintenanceMessage">Do you want to create this request?</p>

                            <div class="buttonContainer">
                                <input type="submit" name="submit" value="Yes"/>
                                <input type="button" onclick="closeMaintenanceConfirm()" value="No">
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="showMaintenanceConfirm()">Request</button>
                </form>
            </div>             
        </div>

        <div class="maintenanceContainer" id="replyContainer">
            <h2 class="innerTitle">Maintenance Request</h2>
            <?php echo $errorDiv1; ?>

            <?php foreach ($maintenances as $maintenance): ?>
                <div class="mainContainer">
                    <div class="valueContainer">
                        <p class="maintenanceField">Apartment ID:</p>
                        <p class="maintenanceValue"><?php echo $maintenance['apartmentID']; ?></p>
                    </div>

                    <div class="valueContainer">
                        <p class="maintenanceField">About:</p>
                        <p class="maintenanceValue"><?php echo $maintenance['about']; ?></p>
                    </div>

                    <div class="valueContainer">
                        <p class="maintenanceField">Request:</p>
                        <p class="maintenanceValue"><?php echo $maintenance['request']; ?></p>
                    </div>

                    <div class="valueContainer">
                        <p class="maintenanceField">Admin's Reply:</p>
                        <p class="maintenanceValue"><?php echo $maintenance['adminReply']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </body>
</html>
</span>