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
                {arag_form uri="user/backend/applications/filters_do_delete"}
                    {foreach from=$objects item=object key=number}
                        <input type="hidden" name="objects[{$number}]" value="{$object}" />
                    {/foreach}
                    <input type="hidden" name="objecttype" value="{$objecttype}" />
                    {if !$objecttype}
                        <input type="hidden" name="application" value="{$appname}" />
                    {/if}
                    <input type="submit" name="submit" value={quote}_("Yes"){/quote} />&nbsp;
                {/arag_form}
            </td>
            <td>
                {if $objecttype}
                    {arag_form uri="user/backend/applications/apps_filters" method="get"}
                        <input type="submit" value={quote}_("No"){/quote} />
                    {/arag_form}
                {else}
                    {arag_form uri="user/backend/applications/app_filters/$appname" method="get"}
                        <input type="submit" value={quote}_("No"){/quote} />
                    {/arag_form}
                {/if}
            </td>
            {if $objecttype}
                <td>
                    &nbsp;
                    <span class="smallfont">_("Notice that all the filters of this(these) application(s) will be deleted too")</span>
                </td>
            {/if}
        </tr>
        </table>
    {/arag_block}
{/arag_block}

