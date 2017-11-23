
$(document).on('click', 'a[href^="https://alitems.com/"]', function(event) {
    if (ga != undefined) {
        ga('send', 'event', {
            eventCategory: 'buy',
            eventAction: 'click',
            eventLabel: event.target.href,
            transport: 'beacon'
        });
    }
});