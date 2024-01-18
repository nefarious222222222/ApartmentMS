<?php
require_once('public/php/database.php');
$imageSrc = $_GET['imageSrc'];

if (isset($_GET['apartNum'])) {
    $apartNum = $_GET['apartNum'];
    echo "<script>console.log($apartNum)</script>";
} else {
    echo "<script>console.log('Apartment Number Invalid')</script>";
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

if($apartNum == 1){
    $firstDescription = $apartments['apartment1']['description'];
    $secondDescription = $apartments['apartment1']['fullInfo'];
} else if($apartNum == 2) {
    $firstDescription = $apartments['apartment2']['description'];
    $secondDescription = $apartments['apartment2']['fullInfo'];
} else if($apartNum == 3) {
    $firstDescription = $apartments['apartment3']['description'];
    $secondDescription = $apartments['apartment3']['fullInfo'];
} else if($apartNum == 4) {
    $firstDescription = $apartments['apartment4']['description'];
    $secondDescription = $apartments['apartment4']['fullInfo'];
} else if($apartNum == 5) {
    $firstDescription = $apartments['apartment5']['description'];
    $secondDescription = $apartments['apartment5']['fullInfo'];
} else if($apartNum == 6) {
    $firstDescription = $apartments['apartment6']['description'];
    $secondDescription = $apartments['apartment6']['fullInfo'];
}
?>
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
                    <?php echo '<img class="apartmentImg" src="' . $imageSrc . '" alt="Apartment Image" />' ?>

                    <p class="firstInfo" id="changeText">
                        <?php echo $firstDescription; ?>
                    </p>
                </div>
    
                <p class="secondInfo">
                    <?php echo $secondDescription; ?>
                </p>
            </div>

            <form>
                <button type="button" onclick="goBack()">Back</button>

                <button type="button"><a href="rentapartment.php<?php if (isset($apartNum)) { echo '?apartNum=' . $apartNum; } ?>">Rent</a></button>     
            </form>
        </div>
    </body>
</html>
</span>