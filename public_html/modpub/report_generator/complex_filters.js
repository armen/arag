window.addEvent('domready', function() {

    $('filter_columns').addEvent('change', function(e) {

        var filter   = $('filter');
        var value     = e.target.get('value');
        var old_value = filter.get('value');

        if (old_value) {
            filter.set('value', old_value+' '+value+' ');
        } else {
            filter.set('value', value+' ');
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

        var myTarget = new Fx.Tween(target);
        myTarget.start('opacity', '1', '0').addEvent('onComplete', function(e) {
            target.empty();
        });
    });


    $$('.filter_operator').addEvent('click', function(e) {

        var filter   = $('filter');
        var value     = e.target.get('value');
        var old_value = filter.get('value');

        if (old_value) {
            filter.set('value', old_value+' '+value+' ');
        } else {
            filter.set('value', value+' ');
        }
    });
});
