{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="modpub/locations/locations.js"}
<div id="container_{$name}">

</div>

{arag_header}
    <script type="text/javascript">
        window.addEvent('domready', function() {literal}{{/literal}
            locations_{$name} = new locations('{$name}', '{$value}', '{kohana_helper function="url::site"}');
        {literal}}{/literal});
    </script>
{/arag_header}