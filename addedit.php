<?php
session_start();
require_once('public/php/database.php');

$title = "Default Title";
$showFormAddContainer = false;
$showEditContainer = false;

$errors = [];
$errorDiv = '';

if (isset($_GET['mode']) && $_GET['mode'] === 'add') {
    $title = "Add Apartment";
    $showFormAddContainer = true;

    if (isset($_POST["submit"])) {
        $fee = isset($_POST["fee"]) ? trim($_POST["fee"]) : '';
        $size = isset($_POST["size"]) ? trim($_POST["size"]) : '';
        $storyNum = isset($_POST["storyNum"]) ? trim($_POST["storyNum"]) : '';
        $status = "available";
        $bedroomNum = isset($_POST["bedroomNum"]) ? trim($_POST["bedroomNum"]) : '';
        $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';
        $fullInfo = isset($_POST["fullInfo"]) ? trim($_POST["fullInfo"]) : '';

        $imageName = $_FILES["image"]["name"];
        $imageSize = $_FILES["image"]["size"];
        $imageTmp = $_FILES["image"]["tmp_name"];

        $allowedEx = array("jpg", "jpeg", "png");
        $imgEx = pathinfo($imageName, PATHINFO_EXTENSION);
        $imgExLc = strtolower($imgEx);

        if (empty($fee) || empty($size) || empty($storyNum) || empty($status) || empty($bedroomNum) || empty($description) || empty($fullInfo) || empty($image)) {
            $errors[] = "All fields are required";
        }

        if ($imageSize > 125000) {
            $errors[] = "Image size is too big";
        } 

        if (empty($errors)) {
            if (in_array($imgExLc, $allowedEx)) {
                $newImgName = uniqid("apartment-", true).'.'.$imgExLc;
                $imgUploadPath = 'uploads/'.$newImgName;
                move_uploaded_file($imageTmp, $imgUploadPath);
            } else {
                echo "<script>alert('Invalid file extension;);</script>";
            }
            
            $insertQuery = "INSERT INTO apartment (fee, size, storyNum, status, bedroomNum, description, fullInfo, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->bind_param('isisisss', $fee, $size, $storyNum, $status, $bedroomNum, $description, $fullInfo, $newImgName);
            $stmtInsert->execute();

            if ($stmtInsert->execute()) {
                echo "<script>alert('Apartment added successfully'); window.location='manageapartment.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error: Unable to add apartment.')</script>;";
            }
        } else {
            foreach ($errors as $error) {
                $errorDiv .= "<div class='alertError'><p>$error</p></div>";
            }
        }
    }
} else if (isset($_GET['mode']) && $_GET['mode'] === 'edit') {
    $title = "Edit Apartment";
    $showEditContainer = true;
}
?>
<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DRCM AMS</title>
        <link rel="stylesheet" href="public/css/addedit.css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap"
            rel="stylesheet"
        />
        <script>
            function showAddConfirm() {
                document.getElementById("addConfirmation").style.display = "block"; 
            }

            function closeAddConfirm() {
                document.getElementById("addConfirmation").style.display = "none";
            }
        </script>
        <style>
           .alertError {
                padding: 10px;
                margin-top: 30px;
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
            <?php echo $title; ?>    
        </h1>

        <div class="addContainer">    
            <?php echo $errorDiv; ?>    
            <?php if ($showFormAddContainer): ?>
                <div class="formAddContainer">
                    <form method="post" action="addedit.php?mode=add" enctype="multipart/form-data">
                        <div class="firstRow">
                            <div class="inputContainer">
                                <label for="image">Choose Image:</label>
                                <input type="file" name="image" id="image">
                            </div>
                                
                            <div class="inputContainer">
                                <label>
                                    Fee:                        
                                </label>
                                <input type="number" class="no-spinner" name="fee"/>
                            </div>

                            <div class="inputContainer">
                                <label>
                                    Size:                        
                                </label>
                                <input type="text" name="size"/>
                            </div>
                        </div>

                        <div class="secondRow">
                            <div class="inputContainer">
                                <label>
                                    Story Number:                        
                                </label>
                                <input type="number" class="no-spinner" name="storyNum"/>
                            </div>

                            <div class="inputContainer">
                                <label>
                                    Bedroom Number:                        
                                </label>
                                <input type="number" class="no-spinner" name="bedroomNum"/>
                            </div>
                        </div>

                        <div class="thirdRow">
                            <label>
                                Description:                        
                            </label>
                            <textarea type="text" name="description"></textarea>

                            <label>
                                Full Info:                        
                            </label>
                            <textarea type="text" name="fullInfo"></textarea>
                        </div>

                        <div class="addConfirmation" id="addConfirmation">
                            <div class="addContent">
                                <h2>Add Apartment</h2>
                                <p class="addMessage">Do you want to add this apartment?</p>

                                <div class="buttonContainer">
                                    <input type="submit" name="submit" value="Yes"/>
                                    <input type="button" onclick="closeAddConfirm()" value="No">
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" onclick="showAddConfirm()">Add Apartment</button>    
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
</span>