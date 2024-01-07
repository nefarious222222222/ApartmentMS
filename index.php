<?php
session_start();
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

            function checkSignIn(event) {
                <?php
                if (!isset($_SESSION["user"])) {
                    echo 'showNotSigned();';
                }
                else {
                    echo 'var targetId = event.target.id;';
                    echo 'if (targetId === "settings") {';
                    echo '  window.location.href = "settings.html";';
                    echo '} else if (targetId === "profile") {';
                    echo '  window.location.href = "profile.html";';
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

                    <ul class="menu">
                        <li><a href="about.html">About</a></li>
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
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment1.jpg" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="apartmentDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.</p>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment2.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment2.jpg" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="apartmentDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.</p>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment3.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment3.jpg" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="apartmentDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.</p>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment4.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment4.jpg" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="apartmentDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.</p>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment5.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment5.jpg" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="apartmentDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.</p>
                    </div>
                </div>
    
                <div class="aboutApartment">
                    <div class="imageContainer">
                        <img class="apartmentImg" src="public/images/apartment6.jpg" alt="Apartment Image" />
                        <a href="apartmentinfo.php?imageSrc=public/images/apartment6.jpg" class="overlayText">Rent</a>
                    </div>
                    <div>
                        <p class="apartmentDetails">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.</p>
                    </div>
                </div>
            </div>
        </section>        
    </body>
</html>
</span>