<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/apartmentinfo.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
        <script src="public/js/redirect.js"></script>
        <script src="public/js/styling.js"></script>
        <script>
            function showNotSigned() {
                document.getElementById("notSignedAlert").style.display = "block";
            }

            function closeNotSigned() {
                window.location.href = 'index.php';
            }

            window.onload = function() {
                <?php
                session_start();
                if (!isset($_SESSION["user"])) {
                    echo 'showNotSigned();';
                }
                ?>
            };   
        </script>
    </head>
    <body>
        <h1 class="title">
            Apartment Information
        </h1>

        <div class="notSignedAlert" id="notSignedAlert">
            <div class="notSignedContent">
                <h2 class="notSignedTitle">Warning</h2>
                <p class="notSignedMessage">You need to be signed in to view this page.</p>

                <div class="buttonContainer">
                    <button type="button" onclick="closeNotSigned()">Okay</button>
                </div>
            </div>
        </div>

        <div class="apartmentInfoContainer">
            <div class="infoGroup">
                <div class="group">
                    <img id="newImg" class="apartmentImg" src="" alt=""/>

                    <p class="firstInfo">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.
                    </p>
                </div>
    
                <p class="secondInfo">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum auctor malesuada odio nec hendrerit. Nulla facilisi. Quisque id felis sed nisi faucibus fringilla vel ac ante. Ut euismod nulla vel quam consequat, eget placerat odio bibendum. Maecenas dignissim lobortis odio, nec volutpat nibh dapibus eu. Aliquam erat volutpat. Integer ullamcorper aliquam urna, eget gravida lectus fermentum ut. Morbi nec odio vitae enim efficitur sollicitudin. Integer consequat justo non lorem interdum, vitae efficitur quam consequat. Etiam vitae consequat ipsum, sit amet consequat nulla. Curabitur lacinia suscipit turpis vel faucibus. Suspendisse potenti. Sed a sollicitudin velit.
                </p>
            </div>

            <form>
                <input type="button" value="Back" onclick="goBack()"/>

                <input type="button" value="Rent" onclick="goRent()"/>     
            </form>

        </div>
    </body>
</html>
</span>