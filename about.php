<?php
session_start();
require_once('public/php/abouttext.php');
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRCM AMS</title>
    <link rel="stylesheet" href="public/css/about.css" />
</head>
<body>
    <div class="aboutusContainer">
        <div class="usType">
            About Us
        </div>

        <div class="aboutContainer">
            <p class="para">
                <?php echo $firstPara; ?>
            </p>

            <br>
            <br>

            <p class="para">
                <?php echo $secondPara; ?>
            </p>

            <br>
            <br>
            
            <p class="para">
                <?php echo $thirdPara; ?>
            </p>
        </div>
    </div>

    <div class="contactusContainer">
        <div class="usType">
            Contact Us
        </div>

        <div class="contactContainer">
            <div class="adminInfo">
                <p class="adminNum">
                    Developer 1
                </p>
                <p class="adminName">
                    Name:<a class="name"> Vince Jeremy Canaria</a>
                </p>
                <p class="adminAge">
                    Age:<a class="age"> 20 Years Old</a>
                </p>
                <p class="adminProgram">
                    Program:<a class="program"> BS InfoTech</a>
                </p>
                <p class="adminSection">
                    Section:<a class="section"> NW3B</a>
                </p>
                <p class="adminEmail">
                    Email:<a class="email"> canariavince@gmail.com</a>
                </p>
            </div>

            <div class="adminInfo">
                <p class="adminNum">
                    Developer 2
                </p>
                <p class="adminName">
                    Name:<a class="name"> Christian Jae G. Dabu</a>
                </p>
                <p class="adminAge">
                    Age:<a class="age"> 20 Years Old</a>
                </p>
                <p class="adminProgram">
                    Program:<a class="program"> BS InfoTech</a>
                </p>
                <p class="adminSection">
                    Section:<a class="section"> NW3B</a>
                </p>
                <p class="adminEmail">
                    Email:<a class="email"> cjgdabu@gmail.com</a>
                </p>
            </div>

            <div class="adminInfo">
                <p class="adminNum">
                    Developer 3
                </p>
                <p class="adminName">
                    Name:<a class="name"> Elizalde John F. Rosario</a>
                </p>
                <p class="adminAge">
                    Age:<a class="age"> 20 Years Old</a>
                </p>
                <p class="adminProgram">
                    Program:<a class="program"> BS InfoTech</a>
                </p>
                <p class="adminSection">
                    Section:<a class="section"> NW3B</a>
                </p>
                <p class="adminEmail">
                    Email:<a class="email"> ejfrosario@gmail.com</a>
                </p>
            </div>

            <div class="adminInfo">
                <p class="adminNum">
                    Developer 4
                </p>
                <p class="adminName">
                    Name:<a class="name"> Unknwon Person</a>
                </p>
                <p class="adminAge">
                    Age:<a class="age"> Unknown Age</a>
                </p>
                <p class="adminProgram">
                    Program:<a class="program"> Unknown Program</a>
                </p>
                <p class="adminSection">
                    Section:<a class="section"> Unknown Section</a>
                </p>
                <p class="adminEmail">
                    Email:<a class="email"> Unknown Email</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
</span>
