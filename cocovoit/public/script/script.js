$(document).ready(function() {
    $('.slider').slick({
        arrows: true,
        prevArrow: '<button class="prev-arrow"></button>',
        nextArrow: '<button class="next-arrow"></button>',
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 3000,
        fade: true,
        cssEase: 'linear'
    });
});


window.addEventListener('DOMContentLoaded', function() {

    const toggleMode = document.getElementById('toggleMode');
    const toggleButton = document.getElementById('toggleButton');

    // Récupérer l'état actuel du mode depuis le localStorage
    const isDarkMode = localStorage.getItem('isDarkMode') === 'true';

    if (isDarkMode){
        document.documentElement.classList.add('dark');
    }

    // Mettre à jour l'état initial du toggle et du mode en fonction du localStorage
    toggleMode.checked = isDarkMode;
    updateMode(isDarkMode);

    toggleMode.addEventListener('change', function() {
        const isDarkMode = this.checked;

        // Mettre à jour le mode en fonction de l'état du toggle
        updateMode(isDarkMode);

        // Sauvegarder l'état du mode dans le localStorage
        localStorage.setItem('isDarkMode', isDarkMode);
    });

    function updateMode(isDarkMode) {
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
            toggleButton.style.transform = 'translateX(100%)';
        } else {
            document.documentElement.classList.remove('dark');
            toggleButton.style.transform = 'translateX(0)';
        }
    }

});