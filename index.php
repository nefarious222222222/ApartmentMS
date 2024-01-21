<?php
session_start();
require_once('public/php/database.php');

if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];

    $sql = "SELECT userType FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userType = $row["userType"];
    }
}

$sqlApart = "SELECT * FROM apartment";
$stmtApart = $conn->prepare($sqlApart);
$stmtApart->execute();
$resultApart = $stmtApart->get_result();

$apartments = [];

if ($resultApart && $resultApart->num_rows > 0) {
    $i = 1;

    while ($row = $resultApart->fetch_assoc()) {
        $variableName = "apartment" . $i++;
        $$variableName = $row;
        $apartments[$variableName] = $row;
    }
} else {
    echo "No apartments found.";
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/homepage.css" />
        <script src="public/js/redirect.js"></script>
        <script src="public/js/styling.js"></script>
        <script>
            function showSignConfirm() {
                document.getElementById("signConfirmation").style.display = "block"; 
            }

            function closeSignConfirm() {
                document.getElementById("signConfirmation").style.display = "none";
            }

            function showNotSigned() {
                document.getElementById("notSignedAlert").style.display = "block"; 
            }

            function closeNotSigned() {
                document.getElementById("notSignedAlert").style.display = "none";
            }

            function showChooseSettings() {
                document.getElementById("chooseSettings").style.display = "block"; 
            }

            function closeChooseSettings() {
                document.getElementById("chooseSettings").style.display = "none";
            }

            function goMaintenance() {
                window.location.href = 'maintenance.php';
            }

            function goManage() {
                window.location.href = 'manageapartment.php';
            }

            function checkSignIn(event) {
                <?php
                if (!isset($_SESSION["user"])) {
                    echo 'showNotSigned();';
                }
                else {
                    echo 'var targetId = event.target.id;';
                    echo 'if (targetId === "settings") {';
                    echo '  showChooseSettings();';
                    echo '} else if (targetId === "profile") {';
                    echo '  window.location.href = "profile.php";';
                    echo '}';
                }
                ?>
            }
        </script>
    </head>
    <body>
        <section id="home" class="introContainer">
            <div class="homeIntro">
                <div class="navBar">
                    <div class="homeLogo">
                        <img class="drcmLogo" src="public/images/logo.png" alt="DRCM Logo" />
                    </div>
                    
                    <div class="signConfirmation" id="signConfirmation">
                        <div class="signContent">
                            <h2 class="signTitle">Sign Out</h2>
                            <p class="signMessage">Do you want to sign out?</p>

                            <div class="buttonContainer">
                                <button type="button" onclick="goSignOut()">Yes</button>
                                <button type="button" onclick="closeSignConfirm()">No</button>
                            </div>
                        </div>
                    </div>

                    <div class="notSignedAlert" id="notSignedAlert">
                        <div class="notSignedContent">
                            <h2 class="notSignedTitle">Warning</h2>
                            <p class="notSignedMessage">You need to be signed in to view that page.</p>

                            <div class="buttonContainer">
                                <button type="button" onclick="closeNotSigned()">Okay</button>
                            </div>
                        </div>
                    </div>

                    <div class="chooseSettings" id="chooseSettings">
                        <div class="chooseSettingsContent">
                            <h2 class="chooseSettingsTitle">Settings</h2>
                            <p class="chooseSettingsMessage">What do you intend to do?</p>

                            <div class="buttonContainer">
                                <button type="button" onclick="goEditInfo()">Edit Information</button>
                                <button type="button" onclick="goChangePass()">Change Password</button>
                                <?php
                                if ($userType == "admin") {
                                    echo '<button type="button" onclick="goManage()">Manage Apartment</button>';
                                } else {
                                    echo '<button type="button" onclick="goMaintenance()">Maintenance Request</button>';
                                }
                                ?>
                                <button type="button" onclick="closeChooseSettings()">Back</button>
                            </div>
                        </div>
                    </div>

                    <ul class="menu">
                        <li><a href="about.php">About</a></li>
                        <li><a id="settings" onclick="checkSignIn(event)">Settings</a></li>
                        <li><a id="profile" onclick="checkSignIn(event)">Profile</a></li>
                        <?php
                        if (isset($_SESSION["user"])) {
                            echo '<style>.menu li { margin-left: 70px; }</style>';
                            echo '<li><a id="signOutLink" onclick="showSignConfirm()">Sign Out</a></li>';
                        } else {
                            echo '<li><a href="signin.php">Sign In</a></li>';
                            echo '<li><a href="signup.php">Sign Up</a></li>';
                        }
                        ?>
                    </ul> 
                </div>
        
                <div class="title">
                    DRCM Apartment<br>Management System
                </div>
            </div>
        </section>
    
        <section class="apartmentContainer">
            <div class="apartmentInformation">
                <?php foreach ($apartments as $apartmentKey => $apartmentInfo): ?>
                    <div class="aboutApartment">
                        <div class="imageContainer">
                            <img class="apartmentImg" src="uploads/<?=$apartmentInfo['imageURL']?>" alt="Apartment Image" />
                            <a href="apartmentinfo.php?imageSrc=uploads/<?=$apartmentInfo['imageURL']?>; ?>&apartNum=<?php echo $apartmentInfo['apartmentID']; ?>" class="overlayText">Rent</a>
                        </div>
                        <div>
                            <p class="info">Apartment Number: <span class="statusValue"><?php echo $apartmentInfo['apartmentID'];?></span></p>
                            <p class="info">Status: <span class="statusValue"><?php echo $apartmentInfo['status'];?></span></p>
                            <div class="groupInfo">
                                <p class="info">Price: <span class="statusValue"><?php echo '₱' . $apartmentInfo['fee'];?></span></p>
                                <p class="info">Size: <span class="statusValue"><?php echo $apartmentInfo['size'];?></span></p>
                                <p class="info">Floors: <span class="statusValue"><?php echo $apartmentInfo['storyNum'] . ' Floor/s';?></span></p>
                                <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartmentInfo['bedroomNum'] . ' Bedroom/s';?></span></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>        
    </body>
</html>
</span>