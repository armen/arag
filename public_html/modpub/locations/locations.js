locations = new Class({
    name : null,
    url  : null,

    initialize : function(name, value, url) {
        this.name = name;
        this.url  = url;
        this.fill(value);
    },

    fill : function(value) {
        this.default_value = value;
        new Request.JSON({
            url      : this.url+'/locations/frontend/search/getSiblings/'+value,
            onSuccess: this.create.bind(this)
        }).get();
    },

    select : function() {
        new_select = new Element('select', {name : this.name+'[]'}).set('class', 'locations_'+this.name);
        new_select.addEvent('change', function(e) { this.refresh(e.target); }.bind(this));
        return new_select;
    },

    option : function(location) {
        var option = new Element('option');

        if (location) {
            option.set({value:location.id});

            if (location.name.length > 1) {
                option.set({html:location.name});
            } else if(location.english.length > 1) {
                option.set({html:location.english});
            } else {
                option.set({html:location.code});
            }
            if (this.default_value == location.id) {
                option.set({selected:'selected'});
            }
        }
        return option;
    },

    container : function() {
        return $('container_'+this.name);
    },

    create : function(locations) {
        if (!locations || locations.length == 0)
            return false;

        var blank = this.option().set({value:'', html:'--', selected:'selected'});
        new_select = this.select();
        new_select.adopt(blank);

        locations.each(function(location) {
            new_select.adopt(this.option(location));
        }.bind(this));

        var br = new Element('br').inject(this.container(), 'after');
        new_select.inject(br, 'after');

        var parent = locations['0']['parent'];

        if (parent > 0) {
            new Request.JSON({
                url      : this.url+'/locations/frontend/search/getSiblings/'+parent,
                onSuccess: function(parents) {
                                this.default_value = parent;
                                this.create(parents);
                           }.bind(this)
            }).get();
        }
    },

    refresh : function(select) {
        select.getAllNext('.'+select.get('class')).dispose(); //Destroy all 'Next' elements.

        if (select.get('value') == '') // Blank is selected, do nothing.
            return false;

        var location = select.get('value'); //Get the selected option's value.

        new Request.JSON({    
            url      : this.url+'/locations/frontend/search/getByParent/'+location,
            onSuccess: function(locations) {
                if (!locations || locations.length == 0)
                    return false;

                var blank = this.option().set({value:'', html:'--', selected:'selected'});
                new_select = this.select();
                new_select.adopt(blank);

                locations.each(function(location) {
                    new_select.adopt(this.option(location));
                }.bind(this));

                var br = new Element('br').inject(select, 'after');
                new_select.inject(br, 'after');
            }.bind(this)
        }).get();
    }
});