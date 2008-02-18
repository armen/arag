{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("New application added applications' filters!")
    {/arag_block}
{/if}
{arag_block}
    {arag_form uri="user/backend/applications/add_apps_filters"}
        <table border="0" dir="{dir}">
            <tr>
                 <td align="{right}">
                    _("Application Name"):
                </td>
                <td align="{left}">
                    <input type="text" name="appname" value="{$appname|smarty:nodefaults|default:null}" />
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
