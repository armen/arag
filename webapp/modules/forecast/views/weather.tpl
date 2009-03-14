{assign var='id' value=weather_activator_`$location.latitude`_`$location.longitude`}
<div id={$id} class="arag_weather">
    <div class="location">
        {$location.name}
    </div>
    <div class="icon">
        <img src="{$arag_base_url}modpub/forecast/icons/{$size}x{$size}/{$weather.conditionIcon}.png" title="{$weather.condition}" alt="{$weather.condition}" />
    </div>
    <div class="temperature">
        {$weather.temperature} °C
    </div>
    <div style="clear:both;"></div>
    {arag_tooltip activator=$id title='_("Details")'}
        <table dir={dir} id='tooltip' class='tooltip'>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/wind.png" alt={quote}_("Wind"){/quote} /> _("Wind"):
                </td>
                <td>
                    {$weather.wind} km/h
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/humidity.png" alt={quote}_("Humidity"){/quote} /> _("Humidity"):
                </td>
                <td>
                    {$weather.humidity}%
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/sunrise.png" alt={quote}_("Sunrise"){/quote} /> _("Sunrise"):
                </td>
                <td>
                    {$location.sunrise}
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/sunset.png" alt={quote}_("Sunset"){/quote} /> _("Sunset"):
                </td>
                <td>
                    {$location.sunset}
                </td>
            </tr>
        </table>
    {/arag_tooltip}
</div>