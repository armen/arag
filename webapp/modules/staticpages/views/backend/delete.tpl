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
                {capture assign="msg"}_("Do you really want to delete '%s'?"){/capture}
                {$msg|sprintf:$subjects}
            </td>
            <td>
                {arag_form uri="staticpages/backend/do_delete"}
                    {foreach from=$ids item=id key=number}
                        <input type="hidden" name="id[{$number}]" value="{$id}" />
                    {/foreach}
                    <input type="submit" name="submit" value={quote}_("Yes"){/quote} />&nbsp;
                {/arag_form}
            </td>
            <td>
                {arag_form uri="staticpages/backend/index"}
                    <input type="submit" name="submit" value={quote}_("No"){/quote} />
                {/arag_form}
            </td>
        </tr>
        </table>
    {/arag_block}
{/arag_block}

