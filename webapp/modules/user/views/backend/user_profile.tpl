{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_load_script src="scripts/mootools/core.js"}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        {capture assign="msg"}_("'%s' edited successfuly!"){/capture}
        {$msg|sprintf:$username}
    {/arag_block}
{/if}
{arag_block}
    {if $flagform}
        {assign var=uri value="user/backend/applications/user_profile/$username"}
    {else}
        {assign var=uri value="user/backend/application/user_profile/$username"}
    {/if}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}" width="100%">
            <tr>
                <td align="{right}" width="150">
                    _("Username"):{asterisk}
                </td>
                <td align="{left}">
                    {$username|smarty:nodefaults|default:null}
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
                        <input type="password" name="oldpassword" dir="ltr" />
                    </td>
                </tr>
            {/if}
            <tr>
                <td align="{right}">
                    _("Password"):
                </td>
                <td align="{left}">
                    <input type="password" name="password" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Re-Password"):
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
            {if !$verified}
                <tr>
                    <td align="{right}">
                        _("Verified"):
                    </td>
                    <td align="{left}">
                            <input type="checkbox" name="verified" />
                    </td>
                </tr>
            {/if}
            <tr>
                <td align="{right}" valign="top">
                    _("Group"):{asterisk}
                </td>
                <td align="{left}">
                    {capture assign=count}{$groups|@count}{/capture}
                    <div id="new_group" style="display:none;">
                        <select name="groups[]">
                            {html_options values=$allgroups|smarty:nodefaults output=$allgroups|smarty:nodefaults}
                        </select>
                        &nbsp;
                        <span>
                        {capture assign="msg"}_("From '%s' application"){/capture}
                        {$msg|sprintf:$appname}
                        </span>
                        <input type="button" value="-" onclick="this.getParent().destroy();" />
                    </div>
                    {foreach from=$groups item=group key=key}
                        <div id="group_{$key+1}">
                            <select name="groups[]">
                                {html_options values=$allgroups|smarty:nodefaults selected=$group|smarty:nodefaults|default:null
                                              output=$allgroups|smarty:nodefaults}
                            </select>
                            &nbsp;
                            <span>
                            {capture assign="msg"}_("From '%s' application"){/capture}
                            {$msg|sprintf:$appname}
                            </span>
                            &nbsp;
                            {if $key == 0}
                                <input type="button" value="+" onclick="$('new_group').clone().injectAfter('group_{$count}').set('style', 'display:block;');" />
                            {else}
                                <input type="button" value="-" onclick="$('group_{$key+1}').destroy();" />
                            {/if}
                        </div>
                    {/foreach}
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
