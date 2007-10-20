{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_block}
    {arag_validation_errors}
    {arag_form uri="user/backend/applications/index"}
        <table border="0" dir="{dir}">
            <tr>
                 <td align="{right}">
                    _("Application Name"):
                </td>
                <td align="{left}">
                    <input type="text" name="name" value="{$name|smarty:nodefaults}" />
                </td>
            </tr>
            <tr>
                 <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Search"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
{arag_block}
    {arag_plist name="applications"}
{/arag_block}
