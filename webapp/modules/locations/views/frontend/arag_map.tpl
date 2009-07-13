{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="scripts/MoodalBox/js/moodalbox.js"}
{arag_load_script src="modpub/locations/MarkerManager.js"}

{arag_header}
        <script type="text/javascript">
            new Asset.javascript('{$arag_base_url}/modpub/locations/arag_map.js', {literal}{ onload: function() {{/literal}
                {$id} = new Arag_Map({literal}{{/literal}
                            id            : '{$id}',
                            key           : '{$key}',
                            minX          : {$minX},
                            maxX          : {$maxX},
                            minY          : {$minY},
                            maxY          : {$maxY},
                            panoramio_url : '{kohana_helper function="url::site" uri="locations/frontend/panoramio"}',
                            weather_url   : '{kohana_helper function="url::site" uri="locations/frontend/weather"}',
                            weather_icons : '{$arag_base_url}/modpub/forecast/icons',
                            weather       : {$show_weather},
                            photos        : {$show_photos},
                            path          : {$show_path}
                {literal}
                });
                {/literal}
                {$id}.addEvent('onload', function() {literal}{{/literal}
                    {foreach from=$path item='dest'} 
                        this.addDestination('{$dest.name}', {$dest.coordinates.1}, {$dest.coordinates.0});
                    {/foreach}
                {literal}
                });
            }});
           {/literal}
        </script>
{/arag_header}

<div id='{$id}' class="map"></div>
