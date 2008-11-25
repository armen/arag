{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="modpub/type_manager/Autocompleter.js"}
{arag_load_script src="modpub/type_manager/Autocompleter.Request.js"}
{arag_load_script src="modpub/type_manager/Observer.js"}

<script type="text/javascript">
{literal}
    window.addEvent('domready', function() {
        new Autocompleter.Request.JSON('search', 'index.php/user/frontend/search', {
            'postVar'  : 'search',
            'className': 'user_autocomplete'
        });
    });
{/literal}
</script>
<input type="text" name="search" id="search"/>