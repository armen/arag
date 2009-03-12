{*Smarty*}
{arag_block}
    {arag_validation_errors}
    {arag_block template="info"}
        {capture assign="info_msg"}
            _("Fields marked with a %s are required.")
        {/capture}
        {asterisk message=$info_msg}
{/arag_block}
{if isset($id|smarty:nodefaults)}
    {assign var=uri value="locations/backend/edit_city/$id"}
{else}
    {assign var=uri value="locations/backend/add_city/`$province.id`"}
{/if}
{arag_form uri=$uri method="post"}
    {arag_block}
         <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">_("Country"):{asterisk}</td>
                <td>
                {if isset($country|smarty:nodefaults)}{$country.country|smarty:nodefaults|default:'&nbsp;'}{/if}
                <input type="hidden" name="country_name" value="{if isset($country|smarty:nodefaults)}{$country.country|smarty:nodefaults|default:null}{/if}"/>
                <input type="hidden" name="country_id" value="{if isset($country|smarty:nodefaults)}{$country.id|smarty:nodefaults|default:null}{/if}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Province"):{asterisk}</td>
                <td>
                {if isset($province|smarty:nodefaults)}{$province.province|smarty:nodefaults|default:'&nbsp;'}{/if}
                <input type="hidden" name="province_name" value="{if isset($province|smarty:nodefaults)}{$province.province|smarty:nodefaults|default:null}{/if}"/>
                <input type="hidden" name="province_id" value="{if isset($province|smarty:nodefaults)}{$province.id|smarty:nodefaults|default:null}{/if}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">_("City"):{asterisk}</td>
                <td>
                <input type="text" name="city" value="{if isset($city|smarty:nodefaults)}{$city|smarty:nodefaults|default:null}{/if}" />
                {if isset($id|smarty:nodefaults)}<input type="hidden" name="id" value="{$id|smarty:nodefaults|default:null}" />{/if}
                </td>
            </tr>
            <tr>
                 <td align="{right}">_("English name"):{asterisk}</td>
                 <td>
                    <input type="text" name="english" value="{if isset($english|smarty:nodefaults)}{$english|smarty:nodefaults|default:null}{/if}" />
                 </td>
             </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value={quote}_("Save"){/quote} /></td>
            </tr>
         </table>

    {/arag_block}
{/arag_form}
{/arag_block}
