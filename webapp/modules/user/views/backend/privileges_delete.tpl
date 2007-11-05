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
                {$msg|sprintf:$subjects|wordwrap:120:"<br />\n"}
            </td>
            <td>
                {arag_form uri="user/backend/applications/privileges_do_delete"}
                    {foreach from=$objects item=object key=number}
                        <input type="hidden" name="objects[{$number}]" value="{$object}" />
                    {/foreach}
                    <input type="submit" name="submit" value={quote}_("Yes"){/quote} />&nbsp;
                {/arag_form}
            </td>
            <td>
                {arag_form uri="user/backend/applications/privileges_parents" method="get"}
                    <input type="submit" value={quote}_("No"){/quote} />        
                {/arag_form}
            </td>            
            {if $flagcaption}
                <td>
                    &nbsp;
                    <span class="smallfont">_("Notice that (all) the sub-privilege(s) of the parent privilege(s) will be deleted too")</span>
                </td>
            {/if}
        </tr>
        </table>
    {/arag_block}
{/arag_block}

