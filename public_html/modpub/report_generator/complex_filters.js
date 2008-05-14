window.addEvent('domready', function() {

    $('filter_columns').addEvent('change', function(e) {

        var filter   = $('filter');
        var value     = e.target.getValue();
        var old_value = filter.getValue();

        if (old_value) {
            filter.setProperty('value', old_value+' '+value+' ');
        } else {
            filter.setProperty('value', value+' ');
        }

        // Unselect selected item with selecting first item on list
        e.target.getFirst().selected = true;
    });

    $('add_filter').addEvent('click', function(e) {

        $('form').submit();
    });

    $$('.remove_filter').addEvent('click', function(e) {

        // Grand parent is <tr>        
        var target = e.target.getParent().getParent();
        var button = e.target;

        new Fx.Styles(target, {
            duration: 500,
            wait: true,
            transition: Fx.Transitions.Quad.easeOut
        }).start({
            'opacity': [1, 0]
        }).addEvent('onComplete', function(e) {
            target.empty();
        });
    });


    $$('.filter_operator').addEvent('click', function(e) {

        var filter   = $('filter');
        var value     = e.target.getProperty('value');
        var old_value = filter.getValue();

        if (old_value) {
            filter.setProperty('value', old_value+' '+value+' ');
        } else {
            filter.setProperty('value', value+' ');
        }
    });
});
