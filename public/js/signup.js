$(document).ready(function() {
    $("#signUpForm").on("submit", function(event) {
        event.preventDefault(); // Prevent the form from redirecting to the PHP file

        // Retrieve form data
        var user = $("#username").val();
        var pass = $("#password").val();
        var confirm = $("#confirmPass").val();
        var email = $("#emailAdd").val();
        var contact = $("#contactNum").val();

        // Submit form data using AJAX
        $.ajax({
            url: "public/php/signup.php",
            type: "POST",
            data: {
                username: user,
                password: pass,
                confirmPass: confirm,
                emailAdd: email,
                contactNum: contact
            },
            success: function(response) {
                console.log(response);
                window.location.href = "signin.html"
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            }
        });
    });
});

/* 

*/