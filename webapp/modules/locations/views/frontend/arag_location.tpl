{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}

{foreach from=$all item='location' key='index'}
    <select name="{$name}[]" class="locations_{$name}" {if $readonly}readonly="readonly"{/if}>
        <option value="">--</option>
        {foreach from=$location.children item='child'}
            {assign var='id' value=$child.id}
            <option value="{$child.id}" {if isset($path.$id|smarty:nodefaults)}selected="selected"{/if}>{$child.english}</option>
        {/foreach}
    </select>
    <br />
{/foreach}

{arag_header}
    <script type="text/javascript">
        function refresh(select) {literal}{{/literal}
                if (select.get('value') == '') // Blank is selected, do nothing.
                    return false;

                select.getAllNext().dispose(); //Destroy all 'Next' elements.
 
                var location = select.get('value'); //Get the selected option's value.

                new Request.JSON({literal}{{/literal}
                    url      : '{kohana_helper function="url::site"}/locations/frontend/search/getByParent/'+location,
                    onSuccess: function(response) {literal}{{/literal}
                        if (!response || response.length == 0)
                            return false;

                        new_select = new Element('select', {literal}{{/literal}
                            name : '{$name}[]',
                            class : 'locations_{$name}'
                        {literal}});{/literal}
                        new_select.addEvent('change', {literal}function(e) { refresh(e.target); }{/literal});

                        var blank = new Element('option', {literal}{value:'', html:'--', selected:'selected'}{/literal});
                        new_select.adopt(blank);

                        response.each(function(location) {literal}{{/literal}
                            var option = new Element('option', {literal}{{/literal}
                                value : location.id,
                                {if $readonly}
                                readonly: 'readonly',
                                {/if}
                                html  : location.english
                            {literal}});{/literal}
                            new_select.adopt(option);
                        {literal}});{/literal}

                        select.getParent().adopt(new Element('br'));
                        select.getParent().adopt(new_select);
                    {literal}}{/literal}

                {literal}}).get();{/literal}
        {literal}}{/literal}

        window.addEvent('domready', function() {literal}{{/literal}
            $$('.locations_{$name}').addEvent('change', {literal}function(e) { refresh(e.target); }{/literal});
        {literal}});{/literal}
    </script>
{/arag_header}