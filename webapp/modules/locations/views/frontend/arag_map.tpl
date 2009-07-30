{arag_load_script src="scripts/mootools/core.js"}
{arag_load_script src="scripts/MoodalBox/js/moodalbox.js"}
{arag_load_script src="modpub/locations/arag_map.js"}
{arag_load_script src="modpub/locations/MarkerManager.js"}

{arag_header}
        <script type="text/javascript">
            window.addEvent('domready', function() {literal}{{/literal}
                var map_{$id} = new Arag_Map({literal}{{/literal}
                            id            : '{$id}',
                            key           : '{$key}',
                            minX          : {$minX},
                            maxX          : {$maxX},
                            minY          : {$minY},
                            maxY          : {$maxY},
                            panoramio_url : '{kohana_helper function="url::site" uri="locations/frontend/panoramio"}',
                            weather_url   : '{kohana_helper function="url::site" uri="locations/frontend/weather"}',
                            weather_icons : '{$arag_base_url}/modpub/forecast/icons'
                {literal}
                });
                {/literal}
                map_{$id}.addEvent('onload', function() {literal}{{/literal}
                                                {foreach from=$path item='dest'} 
                                                    this.addDestination('{$dest.english}', {$dest.latitude}, {$dest.longitude});
                                                {/foreach}

                                            {literal}});{/literal}
           {literal}
            });
           {/literal}
        </script>
{/arag_header}

<div id='{$id}' class="map"></div>
