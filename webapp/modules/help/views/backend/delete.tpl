{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    Author: Emil Sedgh <emilsedgh@gmail.com>
    File:   $Id$
*}
{arag_block align="left"}
    {arag_block align="left" template="warning"}
    <table border="0" dir="{dir}">
    <tr>
        <td>
            {capture assign="msg"}_("Do you really want to delete the '%s' Entry?"){/capture}
            {$msg|sprintf:$help->title}
        </td>
        <td>
            {arag_form uri="help/backend/delete" method="post"}
                <input type="hidden" name="id" value="{$help->id}" />
                <input type="submit" value={quote}_("Yes"){/quote} />
            {/arag_form}
        </td>
        <td>
            {assign var='uri' value=$help->uri}
            {arag_form uri=help/backend/listing/$uri method="get"}
                <input type="submit" value={quote}_("No"){/quote} />
            {/arag_form}
        </td>
    </tr>
    </table>
    {/arag_block}
{/arag_block}
