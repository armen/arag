{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    Author: Peyman Karimi <zeegco@yahoo.com>
    File:   $Id$
*}
{arag_block align="left"}
    {arag_block align="left" template="warning"}
    <table border="0" dir="{dir}">
    <tr>
        <td>
            {capture assign="msg"}_("Do you really want to delete the '%s' Entry?"){/capture}
            {$msg|sprintf:$province.province}
        </td>
        <td>
            {arag_form uri="locations/backend/delete_province" method="post"}
                <input type="hidden" name="province_id" value="{$province.id}" />
                <input type="submit" value={quote}_("Yes"){/quote} />
            {/arag_form}
        </td>
        <td>
            {arag_form uri="locations/backend/list_provinces/`$province.country`" method="get"}
                <input type="submit" value={quote}_("No"){/quote} />
            {/arag_form}
        </td>
    </tr>
    </table>
    {/arag_block}
{/arag_block}
