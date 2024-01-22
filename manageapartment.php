<?php
session_start();
require_once('public/php/database.php');

/* Rent */

$sqlApart = "SELECT * FROM rent";
$stmtApart = $conn->prepare($sqlApart);
$stmtApart->execute();
$resultApart = $stmtApart->get_result();

$rents = [];

if ($resultApart && $resultApart->num_rows > 0) {
    $i = 1;

    while ($row = $resultApart->fetch_assoc()) {
        $variableName = "rent" . $i++;
        $$variableName = $row;
        $rents[$variableName] = $row;
    }
} else {
    echo "No rents found.";
}

if (isset($_POST["submit"])) {
    $statusRent = "accepted";
    $payment = "paid";
    $rentID = $_POST["rentID"];
    
    $pendingCheckQuery = "SELECT status FROM rent WHERE rentID = ?";
    $stmtPending = $conn->prepare($pendingCheckQuery);
    $stmtPending->bind_param('i', $rentID);
    $stmtPending->execute();
    $resultPending = $stmtPending->get_result();

    if ($resultPending && $resultPending->num_rows > 0) {
        $row = $resultPending->fetch_assoc();
        $statusPending = $row["status"];
    } else {
        echo "<script>console.log('Something went wrong');</script>";
    }

    if ($statusPending == "pending") {
        $statusApart = "unavailable";
        $apartmentID = $_POST["apartmentID"];

        $statusCheckQuery = "SELECT status FROM apartment WHERE apartmentID = ?";
        $stmtStatus = $conn->prepare($statusCheckQuery);
        $stmtStatus->bind_param('i', $apartmentID);
        $stmtStatus->execute();
        $resultStatus = $stmtStatus->get_result();

        if ($resultStatus && $resultStatus->num_rows > 0) {
            $row = $resultStatus->fetch_assoc();
            $statusStatus = $row["status"];
        } else {
            echo "<script>console.log('Something went wrong');</script>";
        }

        if ($statusStatus == "available") {
            $updateRentQuery = "UPDATE rent SET status=?, payment=? WHERE rentID=?";
            $stmtRentUpdate = $conn->prepare($updateRentQuery);
            $stmtRentUpdate->bind_param('ssi', $statusRent, $payment, $rentID);
            
            $updateApartmentQuery = "UPDATE apartment SET status=? WHERE apartmentID=?";
            $stmtApartmentUpdate = $conn->prepare($updateApartmentQuery);
            $stmtApartmentUpdate->bind_param('si', $statusApart, $apartmentID);
            
            if ($stmtRentUpdate->execute() && $stmtApartmentUpdate->execute()) {
                echo "<script>alert('You have successfully accepted this rent on apartment ". $apartmentID ."'); window.location='manageapartment.php';</script>";
                exit();
            } else {
                echo "<script>alert('Failed to update apartment status');</script>";
                exit();
            }
        } else {
            echo "<script>alert('This apartment is currently unavailable');</script>";
        }
    } else {
        echo "<script>alert('This transaction has already been accepted');</script>";
    }
}

/* Apartment */

$sqlApartment = "SELECT * FROM apartment";
$stmtApartment = $conn->prepare($sqlApartment);
$stmtApartment->execute();
$resultApartment = $stmtApartment->get_result();

$apartments = [];

if ($resultApartment && $resultApartment->num_rows > 0) {
    while ($row = $resultApartment->fetch_assoc()) {
        $apartments[] = $row;
    }
} else {
    echo "No apartments found.";
}

/* Maintenance */
$sqlMaintenance = "SELECT * FROM maintenance";
$stmtMaintenance = $conn->prepare($sqlMaintenance);
$stmtMaintenance->execute();
$resultMaintenance = $stmtMaintenance->get_result();

$maintenances = [];

if ($resultMaintenance && $resultMaintenance->num_rows > 0) {
    while ($row = $resultMaintenance->fetch_assoc()) {
        $maintenances[] = $row;
    }
} else {
    echo "No maintenance found.";
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/manageapartment.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
        <script src="public/js/redirect.js"></script>
    </head>
    <body>
        <h1 class="title">
            Manage Apartment         
        </h1>

        <div class="divContainer">
            <button class="toggleBtn" type="button" onclick="goBack()">Back</button>
            <button class="toggleBtn" type="button" onclick="toggleRentRequest()">Rent Request</button>
            <button class="toggleBtn" type="button" onclick="toggleManageApartment()">Manage Apartment</button>
            <button class="toggleBtn" type="button" onclick="toggleMaintenanceRequest()">Maintenance Request</button>
        </div>
            
        <div class="rentContainer" id="rentContainer">        
            <h2 class="innerTitle">Pending Rents</h2>
            <div class="rowTitles">
                <table class="rentTable">
                    <thead>
                        <tr>
                            <th class="rentTh">Full Name</th>
                            <th class="rentTh">Move In</th>
                            <th class="rentTh">Move Out</th>
                            <th class="rentTh">Payment Method</th>
                            <th class="rentTh">User ID</th>
                            <th class="rentTh">Apartment ID</th>
                            <th class="rentTh">Status</th>
                            <th class="rentTh">To Pay</th>
                            <th class="rentTh">Payment</th>
                            <th class="rentTh"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rents as $rent) : ?>
                            <tr>
                                <td class="rentTd"><?php echo $rent['fullname']; ?></td>
                                <td class="rentTd"><?php echo $rent['moveIn']; ?></td>
                                <td class="rentTd"><?php echo $rent['moveOut']; ?></td>
                                <td class="rentTd"><?php echo $rent['paymentMethod']; ?></td>
                                <td class="rentTd"><?php echo $rent['userID']; ?></td>
                                <td class="rentTd"><?php echo $rent['apartmentID']; ?></td>
                                <td class="rentTd"><?php echo $rent['status']; ?></td>
                                <td class="rentTd"><?php echo $rent['toPay']; ?></td>
                                <td class="rentTd"><?php echo $rent['payment']; ?></td>
                                <td class="rentTd"><?php
                                    $apartmentID = $rent['apartmentID'];
                                    $rentID = $rent['rentID'];
                                    echo '<button class="acceptBtn" type="button" onclick="showAcceptConfirmation(' . $apartmentID . ',' . $rentID . ')">Accept</button>';
                                ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="apartContainer" id="apartContainer">
            <h2 class="innerTitle">Apartments</h2>
            <button class="addBtn"><a href="addedit.php?mode=add">Add Apartment</a></button>

            <div class="apartmentContainer">
                <?php foreach ($apartments as $apartment): ?>
                    <div class="viewContainer">
                        <div class="dbContainer">
                            <div class="container">
                                <p class="apartmentField">Apartment ID:</p>
                                <p class="apartmentValue"><?php echo $apartment['apartmentID']?></p>
                            </div>

                            <div class="container">
                                <p class="apartmentField">Fee:</p>
                                <p class="apartmentValue"><?php echo '₱' . $apartment['fee']?></p>
                            </div>

                            <div class="container">
                                <p class="apartmentField">Size:</p>
                                <p class="apartmentValue"><?php echo $apartment['size']?></p>
                            </div>

                            <div class="container">
                                <p class="apartmentField">Story Number:</p>
                                <p class="apartmentValue"><?php echo $apartment['storyNum']?></p>
                            </div>

                            <div class="container">
                                <p class="apartmentField">Status:</p>
                                <p class="apartmentValue"><?php echo $apartment['status']?></p>
                            </div>

                            <div class="container">
                                <p class="apartmentField">Bedroom Number:</p>
                                <p class="apartmentValue"><?php echo $apartment['bedroomNum']?></p>
                            </div>
                        </div>

                        <div class="descContainer">
                            <p class="apartmentField">Description:</p>
                            <p class="apartmentValue"><?php echo $apartment['description']?></p>
                        </div>

                        <div class="infoContainer">
                            <p class="apartmentField">Full Information:</p>
                            <p class="apartmentValue"><?php echo $apartment['fullInfo']?></p>
                        </div>

                        <div class="btnContainer">
                            <form method="post">
                                <input type="hidden" name="apartmentID" value="<?php echo $apartment['apartmentID']; ?>" style="display: none;">
                                <button class="editBtn" type="submit" name="editButton">Edit</button>
                                <button class="deleteBtn" type="button" name="deleteButton" onclick="showDeleteConfirmation(<?php echo $apartment['apartmentID']; ?>)">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="maintenanceContainer" id="maintenanceContainer">
            <h2 class="innerTitle">Maintenance Request</h2>

            <?php foreach ($maintenances as $maintenance): ?>
                <div class="mainContainer">
                    <div class="valueContainer">
                        <p class="maintenanceField">Maintenance ID:</p>
                        <p class="maintenanceValue"><?php echo $maintenance['maintenanceID']; ?></p>
                    </div>

                    <div class="valueContainer">
                        <p class="maintenanceField">User ID:</p>
                        <p class="maintenanceValue"><?php echo $maintenance['userID']; ?></p>
                    </div>

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

                    <form method="post">
                        <div class="valueContainer" id="adminReply_<?php echo $maintenance['maintenanceID']; ?>" style="display: none;">
                            <p class="apartmentField">Admin Reply:</p>
                            <textarea type="text" name="adminReply" id="adminReplyTextArea_<?php echo $maintenance['maintenanceID']; ?>"></textarea>
                        </div>

                        <div class="containButton">
                            <button class="replyBtn" type="button" id="submitReply_<?php echo $maintenance['maintenanceID']; ?>" name="submitReply_<?php echo $maintenance['maintenanceID']; ?>" onclick="showReplyConfirmation(<?php echo $maintenance['maintenanceID']; ?>, document.getElementById('adminReplyTextArea_<?php echo $maintenance['maintenanceID']; ?>').value)" style="display: none;" required>Submit Reply</button>
                            <button class="replyBtn" type="button" id="cancelReply_<?php echo $maintenance['maintenanceID']; ?>" onclick="closeAdminReply(<?php echo $maintenance['maintenanceID']; ?>)" style="display: none;">Cancel</button>
                            <button class="replyBtn" type="button" onclick="showAdminReply(<?php echo $maintenance['maintenanceID']; ?>)">Reply</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="acceptConfirmation" id="acceptConfirmation">
            <div class="acceptContent">
                <h2 class="acceptTitle">Confirmation</h2>
                <p class="acceptMessage">Do you want to accept this rental?</p>

                <div class="buttonContainer">
                    <form action="manageapartment.php" method="post">
                        <input type="hidden" id="apartmentIDInput" name="apartmentID" value="">
                        <input type="hidden" id="rentIDInput" name="rentID" value="">
                        <input type="submit" name="submit" value="Yes" />
                        <input type="button" onclick="closeAcceptConfirmation()" value="No" />
                    </form>
                </div>
            </div>
        </div>

        <div class="deleteConfirmation" id="deleteConfirmation">
            <div class="deleteContent">
                <h2 class="deleteTitle">Confirmation</h2>
                <p class="deleteMessage" id="deleteMessage">Do you want to delete this apartment?</p>

                <div class="buttonContainer">
                    <form id="deleteForm" action="manageapartment.php" method="post">
                        <input type="hidden" id="apartmentIDInput" name="apartmentID" value="">
                        <button class="dltBtn" type="submit" name="yesBtn">Yes</button>
                        <button class="dltBtn" type="button" onclick="closeDeleteConfirmation()">No</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="replyConfirmation" id="replyConfirmation">
            <div class="replyContent">
                <h2 class="deleteTitle">Confirmation</h2>
                <p class="replyMessage" id="replyMessage">Do you want to submit this reply on maintenance?</p>

                <div class="buttonContainer">
                    <form id="replyForm" action="manageapartment.php" method="post">
                        <input type="hidden" id="maintenanceIDInput" name="maintenanceID" value="">
                        <input type="hidden" id="adminReplyInput" name="adminReply" value="">
                        <button class="replyBtn" type="submit" name="submitBtn">Submit</button>
                        <button class="replyBtn" type="button" onclick="closeReplyConfirmation()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['editButton'])) {

                session_start();
                $_SESSION['apartmentID'] = $_POST['apartmentID'];
                echo '<script>window.location = "addedit.php?mode=edit";</script>';
                exit();

            } elseif (isset($_POST['deleteButton'])) {

                $_SESSION['apartmentID'] = $_POST['apartmentID'];
                echo '<script>document.getElementById("deleteConfirmation").style.display = "block";</script>';
                
            } elseif (isset($_POST['submitReply_' . $maintenance['maintenanceID']])) {

                $maintenanceIdValue = intval($_POST['maintenanceID']);
                $adminReplyValue = htmlspecialchars($_POST['adminReply']);

                echo '<script>document.getElementById("replyConfirmation").style.display = "block";</script>';
            }
        }
        ?>
        <script>
            function showAcceptConfirmation(apartmentID, rentID) {
                document.getElementById('apartmentIDInput').value = apartmentID;
                document.getElementById('rentIDInput').value = rentID;
                document.getElementById('acceptConfirmation').style.display = 'block';
            }

            function closeAcceptConfirmation() {
                document.getElementById("acceptConfirmation").style.display = "none";
            }

            function closeDeleteConfirmation() {
                document.getElementById("deleteConfirmation").style.display = "none";
            }

            function showDeleteConfirmation(apartmentID) {
                document.getElementById('deleteMessage').innerHTML = "Do you want to delete apartment " + apartmentID + "?";
                document.getElementById("deleteConfirmation").style.display = "block";
                document.getElementById('apartmentIDInput').value = apartmentID;
            }

            function toggleRentRequest() {
                document.getElementById("rentContainer").style.display = "flex";
                document.getElementById("apartContainer").style.display = "none";
                document.getElementById("maintenanceContainer").style.display = "none";
                
            }

            function toggleManageApartment() {
                document.getElementById("apartContainer").style.display = "flex";
                document.getElementById("rentContainer").style.display = "none";
                document.getElementById("maintenanceContainer").style.display = "none";
            }

            function toggleMaintenanceRequest() {
                document.getElementById("maintenanceContainer").style.display = "flex";
                document.getElementById("rentContainer").style.display = "none";
                document.getElementById("apartContainer").style.display = "none";
            }

            function showAdminReply(maintenanceID) {
                document.getElementById('adminReply_' + maintenanceID).style.display = 'block';
                document.getElementById('cancelReply_' + maintenanceID).style.display = 'inline-block';
                document.getElementById('submitReply_' + maintenanceID).style.display = 'inline-block';
            }

            function closeAdminReply(maintenanceID) {
                document.getElementById('adminReply_' + maintenanceID).style.display = 'none';
                document.getElementById('cancelReply_' + maintenanceID).style.display = 'none';
                document.getElementById('submitReply_' + maintenanceID).style.display = 'none';
            }

            function closeReplyConfirmation() {
                document.getElementById("replyConfirmation").style.display = "none";
            }

            function showReplyConfirmation(maintenanceID, adminReplyValue) {
                const escapedAdminReply = adminReplyValue.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                document.getElementById('replyMessage').innerHTML = "Do you want to submit this reply on maintenance " + maintenanceID + "?";
                document.getElementById("replyConfirmation").style.display = "block";
                document.getElementById('maintenanceIDInput').value = maintenanceID;
                document.getElementById('adminReplyInput').value = escapedAdminReply;
            }
    </script>
    <?php
    $apartID =  $apartment['apartmentID'];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['yesBtn'])) {

        $deleteQuery = "DELETE FROM apartment WHERE apartmentID = ?";
        $stmtDelete = $conn->prepare($deleteQuery);

        if ($stmtDelete) {
            $stmtDelete->bind_param('i', $apartID);

            if ($stmtDelete->execute()) {
                echo "<script>alert('Apartment " . $apartID . " deleted successfully');</script>";
                exit();
            } else {
                echo "<script>alert('Apartment " . $apartID . " deletion unsuccessful');</script>";
            }

            $stmtDelete->close();
        } else {
            echo "<script>alert('Error preparing deleting Apartment " . $apartID . "');</script>";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitBtn'])) {
        $maintenanceIdValue = htmlspecialchars($_POST['maintenanceID']);
        $adminReplyValue = htmlspecialchars($_POST['adminReply']);

        if ($adminReplyValue == "") {
            echo '<script>alert("Admin Reply cannot be empty.");</script>';
        } else {
            $updateQuery = "UPDATE maintenance SET adminReply = ? WHERE maintenanceID = ?";
            $stmtUpdate = $conn->prepare($updateQuery);

            if ($stmtUpdate) {
                $stmtUpdate->bind_param('si', $adminReplyValue, $maintenanceIdValue);

                if ($stmtUpdate->execute()) {
                    echo "<script>alert('Maintenance " . $maintenanceIdValue . " updated successfully');</script>";
                } else {
                    echo "<script>alert('Maintenance " . $maintenanceIdValue . " update unsuccessful');</script>";
                }

                $stmtUpdate->close();
            } else {
                echo "<script>alert('Error preparing updating Maintenance " . $maintenanceIdValue . "');</script>";
            }
        }
    }
    ?>
    </body>
</html>
</span>