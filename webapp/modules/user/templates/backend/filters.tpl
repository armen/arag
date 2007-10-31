{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Filters for '{$appname}' application, changed successfuly!")
    {/arag_block}
{/if}
{arag_block}
    {arag_form uri="user/backend/applications/app_filters/$appname"}
        <table border="0" dir="{dir}"> 
             <tr>
                <td align="{right}">
                    _("Add new filter to '{$appname}'"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="filter" value="{$filter|smarty:nodefaults|default = null}" />
                    <input type="hidden" value="{$appname}" name="application" />
                </td>
            </tr> 
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Add"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
{arag_block}
    {arag_plist name="filters_pro"}
{/arag_block}
{arag_block}
    {arag_plist name="filters"}
{/arag_block}
