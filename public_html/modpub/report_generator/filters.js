window.addEvent('domready', function() {

    // {{{ properties

    var operators = {
        'text':    [{text: "contains", value: "~" }, {text: "doesn't contain", value: "!~" }, {text: "begins with", value: "^" },
                    {text: "ends with", value: "$" }, {text: "is", value: "=" }, {text: "is not", value: "!=" }],
        'numeric': [{text: "=", value: "=" }, {text: "!=", value: "!=" }, {text: ">", value: ">" }, {text: "<", value: "<" },
                    {text: "<=", value: "<=" }, {text: ">=", value: ">=" }],
        'date':    [{text: "=", value: "=" }, {text: "!=", value: "!=" }, {text: ">", value: ">" }, {text: "<", value: "<" },
                    {text: "<=", value: "<=" }, {text: ">=", value: ">=" }]
    };

    var combines  = [{text: "OR", value: "OR"}, {text: "AND", value: "AND"}];

    // Create base table which will be used to create filter input fields
    var filters = $('filters');
    var table   = new Element('table', {'border':'0', 'cellpadding':'0', 'cellspacing':'0', 'width':'100%'}).injectInside(filters);

    // }}}
    // {{{ addSelect

    // A function to create select input field
    var addSelect = function(name, options, selected) {
        if (options) {
            var select = new Element('select', {'name': name});
            options.each(function(option) {
                if (option.value == selected) {
                    new Element('option', {'value': option.value, 'selected':'selected'}).set('html', option.text).injectInside(select);
                } else {
                    new Element('option', {'value': option.value}).set('html', option.text).injectInside(select);
                }
            });

            return select;
        }

        return new Element('span');
    }

    // }}}
    // {{{ addFilter

    // A function to add a filter
    var addFilter = function(field, value, selected_operator, selected_combine) {

        if (!$defined(value)) { value = null; }
        if (!$defined(selected_operator)) { selected_operator = null; }
        if (!$defined(selected_combine)) { selected_combine = null; }

        // Each filter has this structure
        //
        // <tr>
        //    +-------+---------+-------------+---------------+
        //    | Label | Operator| Input field | remove button |
        //    +-------+---------+-------------+---------------+
        // </tr>
        //

        // Maximum acceptable length is 60
        var length  = Number(table_desc[field].length).limit(0, 60);
        var tr      = new Element('tr');
        var combine = new Element('td', {'width':'30'}).injectInside(tr);
        var label   = new Element('td', {'align':right_align, 'width':'150'}).injectInside(tr);

        if ($$('#filters table tr').length > 0) {
            addSelect('combines['+field+'][]', combines, selected_combine).injectInside(combine);
        } else {
            new Element('input', {'type':'hidden', 'value':'', 'name':'combines['+field+'][]'}).injectInside(combine);

        }

        tr.injectInside(table);
        label.set('html', field.replace(/_/g, ' ').capitalize());

        /**
         *
         *  var field_exist = $$('#filters .field_'+field);
         *
         *  if (field_exist == '') {
         *      // There is no created instance of this field
         *      tr.injectInside(table);
         *      label.setHTML(field.replace(/_/g, ' ').capitalize());
         *  } else {
         *      // There is created instance of this field so use "or" as label of this
         *      // field and add it after existing field
         *
         *      // Get grand parent of last element which is <tr> and inject "tr" after that
         *      tr.injectAfter($$('#filters .field_'+field).getLast().getParent().getParent());
         *      label.setStyle('color', 'brown').setHTML('or');
         *  }
         */
        var operator = new Element('td', {'class':'operator'}).injectInside(tr);  // Operator
        var input    = new Element('td', {'class':'input'}).injectInside(tr);     // Input
        var remove   = new Element('td', {'class':'remove'}).injectInside(tr);    // Remove button
        var textbox  = new Element('input', {
            'type':'text',
            'size':length,
            'name':'fields['+field+'][]',
            'class':'field_'+field,
            'value':value,
        }).injectInside(input);

        addSelect('operators['+field+'][]', operators[table_desc[field].type], selected_operator).injectInside(operator);

        new Element('input', {'type':'button', 'name': field, 'value':'-'}).injectInside(remove).addEvent('click', function(e) {

            // Grand parent is <tr>
            var target = e.target.getParent().getParent();
            var button = e.target;

            var myTarget = new Fx.Tween(target);
            myTarget.start('opacity', '1', '0').addEvent('onComplete', function(e) {
                target.empty();
            });
        });

        // Create a visual fx then user can find added filter
        var myTr = new Fx.Tween(tr);
        myTr.start('background-color', '#fff692', '#fff');
    }

    // }}}
    // {{{ initialize

    $('filter_fields').addEvent('change', function(e) {

        var field = e.target.get('value');
        addFilter(field);

        // Unselect selected item with selecting first item on list
        e.target.getFirst().selected = true;
    });

    $each(fields, function(values, field){
        $each(values, function(value, index) {
            var selected_operator = fields_operators[field][index];
            var selected_combine  = fields_combines[field][index];
            addFilter(field, value, selected_operator, selected_combine);
        });
    });

    // }}}

});
