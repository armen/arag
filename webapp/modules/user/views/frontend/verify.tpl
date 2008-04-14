{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}
{arag_validation_errors}
    {if $error_message}
        {arag_block template="warning"}
            {$error_message|nl2br}
        {/arag_block}
    {else}
        {arag_block template="info"}   
            _("Please enter your username and password to activate your account")
        {/arag_block}
    {/if}
    {if $show_form}
        {arag_block template="blank"}
        
            {arag_form uri="user/frontend/verify" method="post"}
            <table border="0" dir="{dir}" width="100%">
            <tr>
                <td align="{right}" width="100">_("Username"):</td>
                <td><input type="text" name="username" value="{$username|smarty:nodefaults|default:null}" dir="ltr" /></td>
            </tr>
            <tr>
                <td align="{right}" width="100">_("Password"):</td>
                <td><input type="password" name="password" dir="ltr" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="hidden" name="uri" value="{$uri}" />
                    <input type="submit" value={quote}_("Verify"){/quote} />
                </td>
            </tr>
            </table>
            {/arag_form}

        {/arag_block}
    {/if}
{/arag_block}
