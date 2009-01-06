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
            {if $flagform}
                {assign var=uri value="user/backend/applications/do_delete"}
            {else}
                {assign var=uri value="user/backend/application/do_delete"}
            {/if}
            {arag_form uri=$uri}
                {foreach from=$objects item=object}
                    <input type="hidden" name="objects[]" value="{$object}" />
                {/foreach}
                <input type="hidden" name="flag" value="{$flag}" />
                {if $flag}
                    <input type="hidden" name="application" value="{$appname}" />
                {/if}
                <input type="submit" name="submit" value={quote}_("Yes"){/quote} />&nbsp;
            {/arag_form}
            </td>
            <td>
                {assign var=uri value="$parent_uri"}
                {arag_form uri=$uri method="get"}
                    <input type="submit" name="submit" value={quote}_("No"){/quote} />
                {/arag_form}
            </td>
            {if $flag}
                <td>
                    &nbsp;
                    <span class="smallfont">_("Remember all the users of this group(s) will be deleted too")</span>
                </td>
            {/if}
        </tr>
        </table>
    {/arag_block}
{/arag_block}

