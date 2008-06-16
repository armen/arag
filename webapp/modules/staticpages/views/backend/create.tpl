{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
{arag_block}
    {arag_validation_errors}
    {arag_form uri="staticpages/backend/create"}
        <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{left}">
                _("Subject"):{asterisk}<input type="text" name="subject" value="{$subject|smarty:nodefaults|default:null}" />
            </td>
        </tr>
        <tr>
            <td align="{left}">
                {arag_rte name="page" value=$page|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{left}">
                <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
            </td>
        </tr>
    </table>
    {/arag_form}
{/arag_block}
