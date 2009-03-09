{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        {capture assign="msg"}_("New group added to '%s' application!"){/capture}
        {$msg|sprintf:$appname}
    {/arag_block}
{/if}
{arag_block}
    {if $flagform}
        {assign var=uri value="user/backend/applications/new_group/$appname"}
    {else}
        {assign var=uri value="user/backend/application/new_group/$appname"}
    {/if}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    {capture assign="msg"}_("New Group for '%s'"):{/capture}
                    {$msg|sprintf:$appname}
                </td>
                <td align="{left}">
                    <input type="text" name="newgroup" dir="ltr"/>
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Expiration date"):
                </td>
                <td align="{left}">
                    {arag_date name="expire_date" value=$expire_date}
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="hidden" name="appname" value="{$appname}" />
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
