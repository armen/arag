{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{if !$isset_profile}
    {arag_block align="left" template="warning"}
        _("This user hadn't enter his/her personal information yet")
    {/arag_block}
{/if}
{arag_block}
    <table border="0" dir="{dir}">
        <tr>
            <td align="{right}">
                _("Username"):
            </td>
            <td align="{left}">
                {$username|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Name"):
            </td>
            <td align="{left}">
                {$name|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Last Name"):
            </td>
            <td align="{left}">
                {$lastname|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Email"):
            </td>
            <td align="{left}">
                {$email|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}" colspan="2">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Phone"):{asterisk}
            </td>
            <td align="{left}">
                {$phone|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Cellphone"):
            </td>
            <td align="{left}">
                {$cellphone|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}" colspan="2">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Country"):{asterisk}
            </td>
            <td align="{left}">
                {foreach from=$countries item=item}
                    {if $isset_profile && $item.id == $country}
                        {$item.country}
                    {/if}
                {/foreach}
            </td>
        </tr>
        {if $isset_profile && $country == $defaults.country}
            <tr>
                <td align="{right}">
                    _("Province"):{asterisk}
                </td>
                <td align="{left}">
                    {foreach from=$provinces item=item}
                        {if $isset_profile && $item.id == $province}
                            {$item.province}
                        {/if}
                    {/foreach}
                </td>
            </tr>
        {/if}
        {if $isset_profile && $country == $defaults.country}
            <tr>
                <td align="{right}">
                    _("City"):{asterisk}
                </td>
                <td align="{left}" id="cities">
                    {foreach from=$cities item=item}
                        {if $isset_profile && $item.code == $city}
                            {$item.city}
                        {/if}
                    {/foreach}
                </td>
            </tr>
        {/if}
        <tr>
            <td align="{right}" colspan="2">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Address"):{asterisk}
            </td>
            <td align="{left}">
                {$address|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Postal Code"):{asterisk}
            </td>
            <td align="{left}">
                {$postal_code|smarty:nodefaults|default:null}
            </td>
        </tr>
    </table>
{/arag_block}
