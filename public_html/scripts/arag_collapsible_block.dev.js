window.addEvent('domready', function() {
    $$('.arag_collapsible_block .expanded legend').addEvent('click', function(e) {
        e.target.getParent().toggleClass('arag_collapsible_block collapsed');
    });

    $$('.arag_collapsible_block .collapsed legend').addEvent('click', function(e) {
        e.target.getParent().toggleClass('arag_collapsible_block expanded');
    });    
});
