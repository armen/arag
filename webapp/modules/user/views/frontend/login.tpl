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
            <td align="{right}" width="100">_("Username"):</td>
            <td><input type="text" name="username" value="{$username|smarty:nodefaults|default:null}" /></td>
        </tr>
        <tr>
            <td align="{right}" width="100">_("Password"):</td>
            <td><input type="password" name="password" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>{html_anchor uri="user/frontend/forget_password" title="Forget your Password?"}</td>
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