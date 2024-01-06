<?php
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "apartmentms"; 

    // Create connection
    $conn =  mysqli_connect($servername, $username, $password, $dbname);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmPass = isset($_POST['confirmPass']) ? $_POST['confirmPass'] : '';
        $emailAdd = isset($_POST['emailAdd']) ? $_POST['emailAdd'] : '';
        $contactNum = isset($_POST['contactNum']) ? $_POST['contactNum'] : '';

        if (empty($username)) {
            die("Username is required.");
        } else if (empty($password)) {
            die("Password is required.");
        } else if (empty($confirmPass)) {
            die("Confirm Password is required.");
        } else if ($password !== $confirmPass) {
            die("Passwords do not match.");
        } else if (!filter_var($emailAdd, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email address.");
        } else if (empty($contactNum)) {
        die("Contact Number is required.");
        } else {
            $sql = "INSERT INTO account (username, password, emailAdd, contactNum) VALUES (?, ?, ?, ?)";

            $stmt = mysqli_stmt_init($conn);
    
            if (! mysqli_stmt_prepare($stmt, $sql)){
                die(mysqli_error($conn));
            }
    
            mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $emailAdd, $contactNum);
        }  
    }
    mysqli_stmt_execute($stmt);
    $conn->close();
?>