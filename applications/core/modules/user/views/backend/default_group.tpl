{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Default group for '{$appname}' application, changed successfuly!")
    {/arag_block}
{/if}
{arag_block template="tips"}
    _("Default group is the group which ordinary users will be registered into.")
{/arag_block}
{arag_block}
    {if $flagform}
        {assign var=uri value="user/backend/applications/default_group/$appname"}
    {else}
        {assign var=uri value="user/backend/application/default_group/$appname"}
    {/if}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Default Group for '{$appname}'"):
                </td>
                <td align="{left}">
                    <select name="dgroup">
                        {html_options values=$allgroups|smarty:nodefaults selected=$defaultgroup|smarty:nodefaults|default:null output=$allgroups|smarty:nodefaults}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="hidden" name="appname" value="{$appname}" />
                    <input type="submit" name="submit" value={quote}_("Set"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
