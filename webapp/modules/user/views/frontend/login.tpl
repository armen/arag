{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_block}
{arag_validation_errors}
    {if $error_message}
        {arag_block template="warning"}
            {$error_message|nl2br}
        {/arag_block}
    {/if}
    {arag_block template="blank"}

        {arag_form uri="user/frontend/login" method="post"}
        <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{right}" width="120">_("Username"):</td>
            <td><input type="text" name="username" value="{$username|smarty:nodefaults|default:null}" dir="ltr" /></td>
        </tr>
        <tr>
            <td align="{right}" width="120">_("Password"):</td>
            <td><input type="password" name="password" dir="ltr" /></td>
        </tr>
        {if isset($display_captcha|smarty:nodefaults) && $display_captcha}
        <tr>
            <td align="{right}" width="120">_("Enter letters in image"):</td>
            <td>
                {arag_captcha}
            </td>
        </tr>
        {/if}
        <tr>
            <td>&nbsp;</td>
            <td>{kohana_helper function="html::anchor" uri="user/frontend/forget_password" title="Forget your Password?" attributes='tabindex="100"'}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="submit" value={quote}_("Login"){/quote} />
            </td>
        </tr>
        </table>
        {/arag_form}

    {/arag_block}
{/arag_block}
