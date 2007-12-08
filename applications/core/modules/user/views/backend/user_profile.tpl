{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("{$username} edited successfuly!")
    {/arag_block}
{/if}
{arag_block}
    {if $flagform}
        {assign var=uri value="user/backend/applications/user_profile/$username"}
    {else}
        {assign var=uri value="user/backend/application/user_profile/$username"}
    {/if}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Username"):{asterisk}
                </td>
                <td align="{left}">
                    {$username|smarty:nodefaults|default = null}
                    <input type="hidden" name="username" value="{$username}" />
                    <input type="hidden" value="{$appname}" name="application" />
                </td>
            </tr>
            {if $oldpassword}
                <tr>
                    <td align="{right}">
                        _("Old Password"):
                    </td>
                    <td align="{left}">
                        <input type="password" name="oldpassword" />
                    </td>
                </tr>               
            {/if}
            <tr>
                <td align="{right}">
                    _("Password"):
                </td>
                <td align="{left}">
                    <input type="password" name="password" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Re-Password"):
                </td>
                <td align="{left}">
                    <input type="password" name="repassword" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Blocked"):
                </td>
                <td align="{left}">
                    {if $blocked}
                        <input type="checkbox" name="blocked" checked="checked" />
                    {else}
                        <input type="checkbox" name="blocked" />
                    {/if}
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Group"):{asterisk}
                </td>
                <td align="{left}">
                    <select name="group">
                        {if isset($group|smarty:nodefaults)}{assign var="defaultgroup" value=$group}{/if}
                        {html_options values=$allgroups|smarty:nodefaults selected=$defaultgroup|smarty:nodefaults|default:null output=$allgroups|smarty:nodefaults}
                    </select>
                    &nbsp;<span>from '{$appname}' application</span>
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Name"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="name" value="{$name|smarty:nodefaults|default = null}" />
                </td>
            </tr>           
            <tr>
                <td align="{right}">
                    _("Last Name"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="lastname" value="{$lastname|smarty:nodefaults|default = null}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Email"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="email" value="{$email|smarty:nodefaults|default = null}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
