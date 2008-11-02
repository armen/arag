{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_block}
    {arag_validation_errors}
    {arag_form uri="contact_us/backend/contacts/reply"}
        <table border="0">
            <tr>
                <td align="{left}">
                    _("Subject"):
                </td>
            </tr>
            <tr>
                <td align="{left}">
                    <input type="text" name="subject" value="{$subject|smarty:nodefaults|default:null}" />
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td align="{left}">
                    _("Body"):
                </td>
            </tr>
            <tr>
                <td align="{left}" dir="{dir}">
                    <textarea name="body" rows="10" cols="40" style="text-align:{left};width:600px; height:300px;" dir="{dir}">{$body|smarty:nodefaults|default:null}</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td align="{left}">
                    <input type="hidden" name="email" value="{$email|smarty:nodefaults|default:null}" />
                    <input type="hidden" name="contact_id" value="{$contact_id|smarty:nodefaults|default:null}" />
                    <input type="submit" name="submit" value={quote}_("Send"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
