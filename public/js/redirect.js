/* JS For redirecting pages */

function goBack() {
    window.location.href = 'index.php';
}

function goSignOut() {
    window.location.href = 'signout.php';
}

function goRent() {
    var urlParams = new URLSearchParams(window.location.search);
    var apaNum = urlParams.get('apaNum');

    if (apaNum) {
        window.location.href = "rentapartment.php?apartNum=" + apartNum;
    } else {
        console.error("Apartment number not specified.");
    }
}

function goEditInfo() {
    window.location.href = 'editinfo.php';
}

function goChangePass() {
    window.location.href = 'changepass.html';
}

function goInfo() {
    window.location.href = 'information.html';
}

function goBilling() {
    window.location.href = 'billing.html';
}