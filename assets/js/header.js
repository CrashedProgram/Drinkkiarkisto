/* header.js */

// Sivun ylätunnisteen toiminnallisuus (mobiilivalikko)

document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        // Avaa/sulkee mobiilivalikon
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });

        // Sulkee valikon klikattaessa muualle
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                mobileMenu.classList.remove('open');
            }
        });
    }
});
