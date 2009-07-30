{arag_load_script src="scripts/mootools/core.js"}
{arag_load_script src="modpub/locations/locations.js"}
<div id="container_{$gname}">

</div>

{arag_header}
    <script type="text/javascript">
        window.addEvent('domready', function() {literal}{{/literal}
            locations_{$gname} = new locations('{$gname}', '{$value}', '{kohana_helper function="url::site"}');
        {literal}}{/literal});
    </script>
{/arag_header}
