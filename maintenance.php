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
                    $stmtApaStat->bind_param('i', $apartmentID); // Corrected variable name
                    $stmtApaStat->execute();
                    $resultApaStat = $stmtApaStat->get_result();

                    echo "<script>Apartment Exists</script>";
                    if ($resultApaStat && $resultApaStat->num_rows > 0) {
                        $rowApaStat = $resultApaStat->fetch_assoc();
                        $apartmentStatus = $rowApaStat["status"]; // Corrected variable name

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
            echo "<script>alert('Something went wrong');</script>";
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
        <link rel="stylesheet" href="public/css/maintenance.css" />
         <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
        <script>
            function showMaintenanceConfirm() {
                document.getElementById("maintenanceConfirmation").style.display = "block"; 
            }

            function closeMaintenanceConfirm() {
                document.getElementById("maintenanceConfirmation").style.display = "none";
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
        <div class="changeContainer">        
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
    </body>
</html>
</span>