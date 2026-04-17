const logoutButton = document.getElementById("logout");

if (logoutButton) {
    logoutButton.onclick = function () {
        window.location.href = "logout.php";
    };
}
