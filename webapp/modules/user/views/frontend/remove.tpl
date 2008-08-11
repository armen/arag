{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}
{if $show_form}
    {arag_block template="tips"}
        _("Please enter your username and your email address which you set in your profile to remove the unwanted change password uri.")
    {/arag_block}
{/if}
{arag_validation_errors}
    {if $error_message}
        {arag_block template="warning"}
            {$error_message}
        {/arag_block}
    {/if}
    {if $removed}
        {arag_block template="info"}
            _("The URI removed and is not valid anymore")
        {/arag_block}
    {/if}
    {if $show_form}
        {arag_block template="blank"}

            {arag_form uri="user/frontend/remove" method="post"}
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
                <td>&nbsp;</td>
                <td>
                    <input type="hidden" value="{$verify_uri}" name="verify_uri" />
                    <input type="submit" value={quote}_("Send"){/quote} />
                </td>
            </tr>
            </table>
            {/arag_form}

        {/arag_block}
    {/if}
{/arag_block}
