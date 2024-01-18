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
                                    echo '<button type="button" onclick="goMaintenance()">Manage Apartment</button>';
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
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment1.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment1.jpg&apartNum=1" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="info">Status: <span class="statusValue"><?php echo $apartments['apartment1']['status'];?></span></p>
                        <div class="groupInfo">
                            <p class="info">Price: <span class="statusValue"><?php echo '₱'; echo $apartments['apartment1']['fee'];?></span></p>
                            <p class="info">Size: <span class="statusValue"><?php echo $apartments['apartment1']['size'];?></span></p>
                            <p class="info">Floors: <span class="statusValue"><?php echo $apartments['apartment1']['storyNum']; echo ' Floor/s';?></span></p>
                            <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartments['apartment1']['bedroomNum']; echo ' Bedroom/s';?></span></p>
                        </div>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment2.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment2.jpg&apartNum=2" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="info">Status: <span class="statusValue"><?php echo $apartments['apartment2']['status'];?></span></p>
                        <div class="groupInfo">
                            <p class="info">Price: <span class="statusValue"><?php echo '₱'; echo $apartments['apartment2']['fee'];?></span></p>
                            <p class="info">Size: <span class="statusValue"><?php echo $apartments['apartment2']['size'];?></span></p>
                            <p class="info">Floors: <span class="statusValue"><?php echo $apartments['apartment2']['storyNum']; echo ' Floor/s';?></span></p>
                            <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartments['apartment2']['bedroomNum']; echo ' Bedroom/s';?></span></p>
                        </div>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment3.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment3.jpg&apartNum=3" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="info">Status: <span class="statusValue"><?php echo $apartments['apartment3']['status'];?></span></p>
                        <div class="groupInfo">
                            <p class="info">Price: <span class="statusValue"><?php echo '₱'; echo $apartments['apartment3']['fee'];?></span></p>
                            <p class="info">Size: <span class="statusValue"><?php echo $apartments['apartment3']['size'];?></span></p>
                            <p class="info">Floors: <span class="statusValue"><?php echo $apartments['apartment3']['storyNum']; echo ' Floor/s';?></span></p>
                            <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartments['apartment3']['bedroomNum']; echo ' Bedroom/s';?></span></p>
                        </div>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment4.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment4.jpg&apartNum=4" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="info">Status: <span class="statusValue"><?php echo $apartments['apartment4']['status'];?></span></p>
                        <div class="groupInfo">
                            <p class="info">Price: <span class="statusValue"><?php echo '₱'; echo $apartments['apartment4']['fee'];?></span></p>
                            <p class="info">Size: <span class="statusValue"><?php echo $apartments['apartment4']['size'];?></span></p>
                            <p class="info">Floors: <span class="statusValue"><?php echo $apartments['apartment4']['storyNum']; echo ' Floor/s';?></span></p>
                            <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartments['apartment4']['bedroomNum']; echo ' Bedroom/s';?></span></p>
                        </div>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment5.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment5.jpg&apartNum=5" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="info">Status: <span class="statusValue"><?php echo $apartments['apartment5']['status'];?></span></p>
                        <div class="groupInfo">
                            <p class="info">Price: <span class="statusValue"><?php echo '₱'; echo $apartments['apartment5']['fee'];?></span></p>
                            <p class="info">Size: <span class="statusValue"><?php echo $apartments['apartment5']['size'];?></span></p>
                            <p class="info">Floors: <span class="statusValue"><?php echo $apartments['apartment5']['storyNum']; echo ' Floor/s';?></span></p>
                            <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartments['apartment5']['bedroomNum']; echo ' Bedroom/s';?></span></p>
                        </div>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment6.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment6.jpg&apartNum=6" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="info">Status: <span class="statusValue"><?php echo $apartments['apartment6']['status'];?></span></p>
                        <div class="groupInfo">
                            <p class="info">Price: <span class="statusValue"><?php echo '₱'; echo $apartments['apartment6']['fee'];?></span></p>
                            <p class="info">Size: <span class="statusValue"><?php echo $apartments['apartment6']['size'];?></span></p>
                            <p class="info">Floors: <span class="statusValue"><?php echo $apartments['apartment6']['storyNum']; echo ' Floor/s';?></span></p>
                            <p class="info">Bedrooms: <span class="statusValue"><?php echo $apartments['apartment6']['bedroomNum']; echo ' Bedroom/s';?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>        
    </body>
</html>
</span>