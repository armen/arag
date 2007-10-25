{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_validation_errors}
{if $flagsearch}
    {arag_block} 
        {arag_form uri="user/backend/applications/all_users"}
            <table border="0" dir="{dir}">
                <tr>
                    <td align="{right}">
                        _("Application Name"):
                    </td>
                    <td align="{left}">
                        <input type="text" name="app_name" value="{$app_name|smarty:nodefaults|default = Null}" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Group Name"):
                    </td>
                    <td align="{left}">
                        <input type="text" name="group_name" value="{$group_name|smarty:nodefaults|default = Null}" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("User"):
                    </td>
                    <td align="{left}">
                        <input type="text" name="user" value="{$user|smarty:nodefaults|default = Null}" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                    </td>
                    <td align="{left}">
                        <input type="submit" name="submit" value={quote}_("Search"){/quote} />
                    </td>
                </tr>
            </table>
        {/arag_form}
    {/arag_block} 
{/if}
{arag_block}
    {arag_plist name="users"}
{/arag_block}
