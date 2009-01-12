{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    Author: Armen baghumian <armen@OpenSourceClub.org>
    File:   $Id$
*}
{arag_block align="left"}
    {arag_block align="left" template="warning"}
    <table border="0" dir="{dir}">
    <tr>
        <td>
            {capture assign="msg"}_("Do you really want to delete the '%s' consumer?"){/capture}
            {$msg|sprintf:$requester}
        </td>
        <td>
            {arag_form uri="oauth/backend/delete" method="post"}
                <input type="hidden" name="user_id" value="{$user_id}" />
                <input type="hidden" name="consumer_key" value="{$consumer_key}" />
                <input type="submit" value={quote}_("Yes"){/quote} />
            {/arag_form}
        </td>
        <td>
            {arag_form uri="oauth/backend/consumers" method="post"}
                <input type="submit" value={quote}_("No"){/quote} />
            {/arag_form}
        </td>
    </tr>
    </table>
    {/arag_block}
{/arag_block}
