document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const menuLinks = document.querySelector('.topbar .actions > div:last-child'); // Seleciona o container de links

    if (mobileMenuToggle && menuLinks) {
        mobileMenuToggle.addEventListener('click', function () {
            menuLinks.classList.toggle('open');
            mobileMenuToggle.classList.toggle('active');
        });
    }
});
