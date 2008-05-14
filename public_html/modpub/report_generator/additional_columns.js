window.addEvent('domready', function() {

    $('additional_column_columns').addEvent('change', function(e) {

        var formula   = $('formula');
        var value     = e.target.getValue();
        var old_value = formula.getValue();

        if (old_value) {
            formula.setProperty('value', old_value+' '+value+' ');
        } else {
            formula.setProperty('value', value+' ');
        }

        // Unselect selected item with selecting first item on list
        e.target.getFirst().selected = true;
    });

    $('add_additional_column').addEvent('click', function(e) {

        $('form').submit();
    });

    $$('.remove_additional_column').addEvent('click', function(e) {

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


    $$('.column_operator').addEvent('click', function(e) {

        var formula   = $('formula');
        var value     = e.target.getProperty('value');
        var old_value = formula.getValue();

        if (old_value) {
            formula.setProperty('value', old_value+' '+value+' ');
        } else {
            formula.setProperty('value', value+' ');
        }
    });
});
