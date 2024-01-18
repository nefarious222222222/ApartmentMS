<?php
session_start();
require_once('public/php/database.php');

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
    $status = "accepted";
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
        $status = "unavailable";
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

        if ($statusStatus == "pending") {
            $updateRentQuery = "UPDATE rent SET status=?, payment=? WHERE rentID=?";
            $stmtRentUpdate = $conn->prepare($updateRentQuery);
            $stmtRentUpdate->bind_param('ssi', $status, $payment, $rentID);
            
            $updateApartmentQuery = "UPDATE apartment SET status=? WHERE apartmentID=?";
            $stmtApartmentUpdate = $conn->prepare($updateApartmentQuery);
            $stmtApartmentUpdate->bind_param('si', $status, $apartmentID);
            
            if ($stmtRentUpdate->execute() || $stmtApartmentUpdate->execute()) {
                echo "<script>alert('You have successfully accepted this rent'); window.location='manageapartment.php';</script>";
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
    </head>
    <body>
        <h1 class="title">
            Manage Apartment         
        </h1>
            
        <div class="divContainer">        
            <h2 class="innerTitle">Pending Rents</h2>
            <div class="rowTitles">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Move In</th>
                            <th>Move Out</th>
                            <th>Payment Method</th>
                            <th>User ID</th>
                            <th>Apartment ID</th>
                            <th>Status</th>
                            <th>To Pay</th>
                            <th>Payment</th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rents as $rent) : ?>
                            <tr>
                                <td><?php echo $rent['fullname']; ?></td>
                                <td><?php echo $rent['moveIn']; ?></td>
                                <td><?php echo $rent['moveOut']; ?></td>
                                <td><?php echo $rent['paymentMethod']; ?></td>
                                <td><?php echo $rent['userID']; ?></td>
                                <td><?php echo $rent['apartmentID']; ?></td>
                                <td><?php echo $rent['status']; ?></td>
                                <td><?php echo $rent['toPay']; ?></td>
                                <td><?php echo $rent['payment']; ?></td>
                                <td><?php
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

        <script>
            function showAcceptConfirmation(apartmentID, rentID) {
                document.getElementById('apartmentIDInput').value = apartmentID;
                document.getElementById('rentIDInput').value = rentID;
                document.getElementById('acceptConfirmation').style.display = 'block';
            }

            function closeAcceptConfirmation() {
                document.getElementById("acceptConfirmation").style.display = "none";
            }
    </script>
    </body>
</html>
</span>