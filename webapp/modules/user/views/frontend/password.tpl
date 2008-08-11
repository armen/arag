{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}
    {arag_block template="tips"}
        _("Please enter your username and your email address which you set in your profile to recieve instructions for changing password.")
    {/arag_block}
    {arag_validation_errors}
    {if $message}
        {arag_block template="error"}
            {$message}
        {/arag_block}
    {/if}
    {if $error_message}
        {arag_block template="warning"}
            {$error_message}
        {/arag_block}
    {/if}
    {if $is_sent}
        {arag_block template="info"}
            _("Please follow the instruction contained in an email sent to your email address, to complete the proccess.")
        {/arag_block}
    {/if}
    {arag_block template="blank"}

        {arag_form uri="user/frontend/forget_password" method="post"}
        <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{right}" width="150">_("Username"):</td>
            <td><input type="text" name="username" value="{$username|smarty:nodefaults|default:null}" dir="ltr"/></td>
        </tr>
        <tr>
            <td align="{right}" width="150">_("Your Email Address"):</td>
            <td><input type="text" name="email" value="{$email|smarty:nodefaults|default:null}" dir="ltr" /></td>
        </tr>
        <tr>
            <td align="{right}">_("Enter the text of image in text box.")</td>
            <td align="{left}">{arag_captcha}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="submit" value={quote}_("Send"){/quote} />
            </td>
        </tr>
        </table>
        {/arag_form}

    {/arag_block}
{/arag_block}
