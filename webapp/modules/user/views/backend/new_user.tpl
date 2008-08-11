{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        {capture assign="msg"}_("New user added to '%s' application!"){/capture}
        {$msg|sprintf:$appname}
    {/arag_block}
{/if}
{arag_block}
    {if $flagform}
        {assign var=uri value="user/backend/applications/new_user/$appname"}
    {else}
        {assign var=uri value="user/backend/application/new_user/$appname"}
    {/if}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Username"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="username" value="{$username|smarty:nodefaults|default:null}" dir="ltr" />
                    <input type="hidden" value="{$appname}" name="appname" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Password"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="password" name="password" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Re-Password"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="password" name="repassword" dir="ltr" />
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
                    _("Group"):{asterisk}
                </td>
                <td align="{left}">
                    <select name="group">
                        {if isset($group|smarty:nodefaults)}{assign var="defaultgroup" value=$group}{/if}
                        {html_options values=$allgroups|smarty:nodefaults selected=$defaultgroup|smarty:nodefaults|default:null 
                                      output=$allgroups|smarty:nodefaults}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Name"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="name" value="{$name|smarty:nodefaults|default:null}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Last Name"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="lastname" value="{$lastname|smarty:nodefaults|default:null}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Email"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="email" value="{$email|smarty:nodefaults|default:null}" dir="ltr" />
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
