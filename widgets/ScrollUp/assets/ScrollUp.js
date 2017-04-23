$(document).ready(function() {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 0) {
            $('.btn-scroll-up').fadeIn();
        } else {
            $('.btn-scroll-up').fadeOut();
        }
    });

    $('.btn-scroll-up').click(function () {
        $('body, html').animate({scrollTop: 0}, 400);
        return false;
    });
});