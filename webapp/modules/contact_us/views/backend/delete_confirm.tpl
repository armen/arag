{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_block}
    {arag_block template="warning"}
        <table border="0" dir="{dir}">
            <tr>
                <td>
                {capture assign="msg"}_("Do you really want to delete this contact : '%s'?"){/capture}
                {$msg|sprintf:$contact_title}
                </td>
                <td>
                {arag_form uri="contact_us/backend/contacts/delete" method="post"}
                    <input type="submit" name="submit" value={quote}_("Yes"){/quote} />
                    <input type="hidden" name="contact_id" value="{$contact_id}" />
                {/arag_form}
                </td>
                <td>
                {arag_form uri="contact_us/backend/contacts" method="get"}
                    <input type="submit" name="submit" value={quote}_("No"){/quote} />
                {/arag_form}
                </td>
            </tr>
        </table>
    {/arag_block}
{/arag_block}
