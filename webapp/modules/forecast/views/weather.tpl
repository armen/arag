<div class="arag_weather">
    <div class="location">
        {$location.name}
    </div>
    <div class="icon">
        <img src="{$arag_base_url}modpub/forecast/icons/{$size}x{$size}/{$weather.conditionIcon}.png" title="{$weather.condition}"/>
    </div>
    <div class="temperature">
        {$weather.temperature} °C
    </div>
    <img id="activator" src="{$arag_base_url}modpub/forecast/icons/zoom.png" style="float:{right};" />
    <div style="clear:both;"></div>
    {arag_tooltip activator='activator' title='_("Details")'}
        <table dir={dir} id='tooltip' class='tooltip'>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/wind.png" /> _("Wind"):
                </td>
                <td>
                    {$weather.wind} km/h
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/humidity.png" /> _("Humidity"):
                </td>
                <td>
                    {$weather.humidity}%
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/sunrise.png" /> _("Sunrise"):
                </td>
                <td>
                    {$location.sunrise}
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{$arag_base_url}modpub/forecast/icons/sunset.png" /> _("Sunset"):
                </td>
                <td>
                    {$location.sunset}
                </td>
            </tr>
        </table>
    {/arag_tooltip}
</div>