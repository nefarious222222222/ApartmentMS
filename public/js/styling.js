/* JS for styling like DOM */

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const imageSrc = params.get('imageSrc');

    var newImage = document.getElementById('newImg');

    if (imageSrc) {
        newImage.src = imageSrc;
    } 
});

function showCancel() {
    document.getElementById('cancelConfirmation').style.display = "block";
}

function closeCancel() {
    document.getElementById("cancelConfirmation").style.display = "none";
}

function showRent() {
    document.getElementById('rentConfirmation').style.display = "block";
}

function closeRent() {
    document.getElementById('rentConfirmation').style.display = "none";
}

function showAlert() {
    document.getElementById('customAlert').style.display = "block";
}

function closeAlert() {
    window.location.href = 'index.html';
}