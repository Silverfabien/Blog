import $ from 'jquery';

$(document).ready(function() {
    // Gestion du menu mobile
    $('.nav-trigger').click(function() {
        $(this).toggleClass('active');

        // On retire 'hidden' si présent, puis on utilise fadeToggle pour l'animation
        $('#mobile-menu').toggleClass('hidden');

        // Animation des barres du burger
        if($(this).hasClass('active')) {
            $(this).find('i:nth-child(1)').css('transform', 'translateY(10px) rotate(45deg)');
            $(this).find('i:nth-child(2)').css('opacity', '0');
            $(this).find('i:nth-child(3)').css('transform', 'translateY(-10px) rotate(-45deg)');
        } else {
            $(this).find('i').css({'transform': 'none', 'opacity': '1'});
        }
    });

    // Gestion du scroll
    $(window).scroll(function() {
        if ($(document).scrollTop() > 50) {
            // Équivalent de .nav-scroll : Fond noir, padding réduit, bordure
            $('#navbar').addClass('bg-black/90 backdrop-blur-md py-2 border-b border-white/10 shadow-lg').removeClass('py-5 bg-transparent');
        } else {
            // État initial : Transparent, plus large
            $('#navbar').removeClass('bg-black/90 backdrop-blur-md py-2 border-b border-white/10 shadow-lg').addClass('py-5 bg-transparent');
        }
    });
})
