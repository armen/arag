{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="modpub/locations/locations.js"}
<script type="text/javascript">

    var getProvincesBaseUrl = '{kohana_helper function="url::site" uri="locations/frontend/search/get_provinces_of"}/';
    var getCitiesBaseUrl    = '{kohana_helper function="url::site" uri="locations/frontend/search/get_cities_of"}/';

    {literal}
    window.addEvent('domready', function() {

        {/literal}
        var country_name  = '{$country_name}';
        var city_name     = '{$city_name}';
        var province_name = '{$province_name}';
        {literal}

        initLocations(country_name, province_name, city_name);

        {/literal}
        {if empty($cities|smarty:nodefaults)}updateSelect(new Array(), city_name);{/if}
        {if empty($provinces|smarty:nodefaults)}updateSelect(new Array(), province_name);{/if}
        {literal}
    });
    {/literal}
</script>
<select id="{$country_name}" name="{$country_name}" style="width:{$width}px">
    <option value="">&nbsp;</option>
    {foreach from=$countries item=entry}
        <option value="{$entry.id}"{if isset($country|smarty:nodefaults) && $entry.id == $country} selected="selected"{/if}>
            {$entry.country|smarty:nodefaults|default:"&nbsp;"}
        </option>
    {foreachelse}
        <option value="">&nbsp;</option>
    {/foreach}
</select>
<div id="{$province_name}_container">
    <select id="{$province_name}" name="{$province_name}" style="width:{$width}px;">
        <option value="">&nbsp;</option>
        {foreach from=$provinces item=entry}
            <option value="{$entry.id}"{if isset($province|smarty:nodefaults) && $entry.id == $province} selected="selected"{/if}>
                {$entry.province|default:"&nbsp;"}
            </option>
        {foreachelse}
            <option value="">&nbsp;</option>
        {/foreach}
    </select>
</div>
<div id="{$city_name}_container">
    <select id="{$city_name}" name="{$city_name}" style="width:{$width}px">
        <option value="">&nbsp;</option>
        {foreach from=$cities item=entry}
            <option value="{$entry.code}"{if isset($city|smarty:nodefaults) && $entry.code == $city} selected="selected"{/if}>
                {$entry.city|default:"&nbsp;"}
            </option>
        {foreachelse}
            <option value="">&nbsp;</option>
        {/foreach}
    </select>
</div>
