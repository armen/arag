{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}
    {arag_validation_errors}
    {if $message}
        {arag_block template="error"}
            {$message}
        {/arag_block}
    {/if}
    {if $flagsaved}
        {if $is_sent}
            {arag_block align="left" template="info"}
                _("Your username is now registered. An email sent to you containing an activation link.")
            {/arag_block}
        {else}
            {arag_block align="left" template="error"}
                _("Your username is now registered. But there was a problem sending you an activation email.")
            {/arag_block}
        {/if}
    {/if}
    {arag_form uri="user/frontend/registration"}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Username"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="username" value="{$username|smarty:nodefaults|default:null}" dir="ltr" />
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
                    _("Re-Email"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="reemail" dir="ltr"/>
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Type the text you see in image"):{asterisk}</td>
                <td align="{left}">
                    {arag_captcha}
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
