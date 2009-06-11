{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="modpub/user/Autocompleter.js"}
{arag_load_script src="modpub/user/Autocompleter.Request.js"}
{arag_load_script src="modpub/user/Observer.js"}

<script type="text/javascript">
{literal}
    window.addEvent('domready', function() {
        new Autocompleter.Request.JSON('{/literal}{$name|smarty:nodefaults|default:null}_id{literal}', '{/literal}{kohana_helper function="url::site"}{literal}user/frontend/search', {
            'postVar'  : '{/literal}username{literal}',
            'className': 'user_autocomplete'
        });
    });
{/literal}
</script>
<input type="text" name="{$name|smarty:nodefaults|default:null}" id="{$name|smarty:nodefaults|default:null}_id" value="{$value|smarty:nodefaults|default:null}" />
