{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("New group added to '{$appname}' application!")
    {/arag_block}
{/if}
{arag_block}
    {arag_form uri="user/backend/applications/new_group"}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("New Group for '{$appname}'"):
                </td>
                <td align="{left}">
                    <input type="text" name="newgroup" />
                    <input type="hidden" value="{$appname}" name="application" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
