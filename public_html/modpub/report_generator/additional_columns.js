window.addEvent('domready', function() {

    $('additional_column_columns').addEvent('change', function(e) {

        var formula   = $('formula');
        var value     = e.target.get('value');
        var old_value = formula.get('value');

        if (old_value) {
            formula.set('value', old_value+' '+value+' ');
        } else {
            formula.set('value', value+' ');
        }

        // Unselect selected item with selecting first item on list
        e.target.getFirst().selected = true;
    });

    $('add_additional_column').addEvent('click', function(e) {

        $('form').submit();
    });

    $$('.remove_additional_column').addEvent('click', function(e) {

        e.stop();

        // Grand parent is <tr>
        var target = e.target.getParent().getParent();
        var button = e.target;

        var myTarget = new Fx.Tween(target);
        myTarget.start('opacity', '1', '0').addEvent('onComplete', function(e) {
            target.empty();
        });
    });


    $$('.column_operator').addEvent('click', function(e) {

        var formula   = $('formula');
        var value     = e.target.get('value');
        var old_value = formula.get('value');

        if (old_value) {
            formula.set('value', old_value+' '+value+' ');
        } else {
            formula.set('value', value+' ');
        }
    });
});
