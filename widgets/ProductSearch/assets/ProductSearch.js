$(document).ready(function() {
    $('.read-more').click(function(e) {
        e.preventDefault();

        var block = $(this).parent();
        block.parent().animate({height:'100%'});
        block.hide();
        block.next().hide();
    });
});