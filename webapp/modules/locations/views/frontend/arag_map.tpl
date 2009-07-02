{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="scripts/MoodalBox/js/moodalbox.js"}
{arag_load_script src="modpub/locations/arag_map.js"}
{arag_load_script src="scripts/MarkerManager.js"}

{arag_header}
        <script type="text/javascript">
            window.addEvent('domready', function() {literal}{{/literal}
                alert('domready');
                var new_map = new Arag_Map({literal}{{/literal}
                            id            : '{$id}',
                            key           : '{$key}',
                            minX          : {$minX},
                            maxX          : {$maxX},
                            minY          : {$minY},
                            maxY          : {$maxY},
                            panoramio_url : '{kohana_helper function="url::site" uri="locations/frontend/panoramio"}',
                            weather_url   : '{kohana_helper function="url::site" uri="locations/frontend/weather"}',
                            weather_icons : '{$arag_base_url}/modpub/forecast/icons',
                            onload        : function() {literal}{{/literal}
                                                {foreach from=$path item='dest'} 
                                                    this.addDestination('{$dest.name}', {$dest.coordinates.1}, {$dest.coordinates.0});
                                                {/foreach}

                                            {literal}}{/literal}
                {literal}
                });
                {/literal}
                
           {literal}
            });
           {/literal}
        </script>
{/arag_header}

<div id='{$id}'></div>