
$(document).on('click', 'a[href^="https://alitems.com/"]', function(event) {
    event.preventDefault();
    
    if (ga != undefined) {
        ga('set', 'transport', 'beacon');
        ga('send', 'event', {
            eventCategory: 'buy',
            eventAction: 'click',
            eventLabel: event.target.href,
            transport: 'beacon',
            hitCallback: function() {
                window.location.href = event.target.href;
            }
        });
    }
});