document.addEventListener("DOMContentLoaded", function () {
    var menuBtn = document.getElementById("menu-btn");
    var mobileMenu = document.getElementById("mobile-menu");
    var iconMenu = document.getElementById("icon-menu");
    var iconClose = document.getElementById("icon-close");

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener("click", function () {
            var isHidden = mobileMenu.classList.toggle("hidden");
            iconMenu.classList.toggle("hidden", !isHidden);
            iconClose.classList.toggle("hidden", isHidden);
        });

        document.querySelectorAll(".nav-link-mobile").forEach(function (link) {
            link.addEventListener("click", function () {
                mobileMenu.classList.add("hidden");
                iconMenu.classList.remove("hidden");
                iconClose.classList.add("hidden");
            });
        });
    }
});