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
    {arag_block template="blank"}
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
        {if $phone|smarty:nodefaults|default:null}
        <tr>
            <td align="{right}">
                _("Phone"):
            </td>
            <td align="{left}">
                {$phone|smarty:nodefaults|default:null}
            </td>
        </tr>
        {/if}
        {if $cellphone|smarty:nodefaults|default:null}
        <tr>
            <td align="{right}">
                _("Cellphone"):
            </td>
            <td align="{left}">
                {$cellphone|smarty:nodefaults|default:null}
            </td>
        </tr>
        {/if}
        {if $isset_profile}
            <tr>
                <td align="{right}">
                    _("Country"):
                </td>
                <td align="{left}">
                    {foreach from=$countries|smarty:nodefaults|default:null item=item}
                        {if $isset_profile && $item.id == $country}
                            {$item.country}
                        {/if}
                    {/foreach}
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Province"):
                </td>
                <td align="{left}">
                    {foreach from=$provinces|smarty:nodefaults|default:null item=item}
                        {if $isset_profile && $item.id == $province}
                            {$item.province}
                        {/if}
                    {/foreach}
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("City"):
                </td>
                <td align="{left}" id="cities">
                    {foreach from=$cities|smarty:nodefaults|default:null item=item}
                        {if $isset_profile && $item.code == $city}
                            {$item.city}
                        {/if}
                    {/foreach}
                </td>
            </tr>
        {/if}
        {if $address|smarty:nodefaults|default:null}
        <tr>
            <td align="{right}">
                _("Address"):
            </td>
            <td align="{left}">
                {$address|smarty:nodefaults|default:null}
            </td>
        </tr>
        {/if}
        {if $postal_code|smarty:nodefaults|default:null}
        <tr>
            <td align="{right}">
                _("Postal Code"):
            </td>
            <td align="{left}">
                {$postal_code|smarty:nodefaults|default:null}
            </td>
        </tr>
        {/if}
    </table>
    {/arag_block}
{/arag_block}
