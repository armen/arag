{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="modpub/locations/locations.js"}
<script type="text/javascript">

    var getProvincesBaseUrl = '{kohana_helper function="url::site" uri="locations/frontend/get_provinces_of"}/';
    var getCitiesBaseUrl    = '{kohana_helper function="url::site" uri="locations/frontend/get_cities_of"}/';

    {literal}
    window.addEvent('domready', function() {

        initLocations({/literal}'{$prefix}'{literal});

        {/literal}
        {if empty($cities|smarty:nodefaults)}updateSelect(new Array(), '{$prefix}city');{/if}
        {if empty($provinces|smarty:nodefaults)}updateSelect(new Array(), '{$prefix}province');{/if}
        {literal}
    });
    {/literal}
</script>
<select id="{$prefix}country" name="{$prefix}country" style="width:180px">
    <option value="0">&nbsp;</option>
    {foreach from=$countries item=entry}
        <option value="{$entry.id}"{if isset($country|smarty:nodefaults) && $entry.id == $country} selected="selected"{/if}>
            {$entry.country|smarty:nodefaults|default:"&nbsp;"}
        </option>
    {/foreach}
</select>
<div id="{$prefix}province_container">
    <select id="{$prefix}province" name="{$prefix}province" style="width:180px;">
        {foreach from=$provinces item=entry}
            <option value="{$entry.id}"{if isset($province|smarty:nodefaults) && $entry.id == $province} selected="selected"{/if}>
                {$entry.province|default:"&nbsp;"}
            </option>
        {foreachelse}
            <option value="0">&nbsp;</option>
        {/foreach}
    </select>
</div>
<div id="{$prefix}city_container">
    <select id="{$prefix}city" name="{$prefix}city" style="width:180px">
        {foreach from=$cities item=entry}
            <option value="{$entry.code}"{if isset($city|smarty:nodefaults) && $entry.code == $city} selected="selected"{/if}>
                {$entry.city|default:"&nbsp;"}
            </option>
        {foreachelse}
            <option value="0">&nbsp;</option>
        {/foreach}
    </select>
</div>
