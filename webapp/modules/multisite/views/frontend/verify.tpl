{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_block}
    {if $error_message}
        {arag_block template="warning"}
            {$error_message}
        {/arag_block}
    {else}
        {arag_block template="info"}   
            _("Please enter your username and password to activate your account")
        {/arag_block}
    {/if}
    
    {if $show_form}
        {arag_block template="blank"}
            {arag_form uri="multisite/frontend/index" method="post"}
            <table border="0" dir="{dir}" width="100%">
            <tr>
                <td align="{right}" width="100">_("User Name"):</td>
                <td><input type="text" name="username" value="{$limit|smarty:nodefaults|default:null}" /></td>
            </tr>
            <tr>
                <td align="{right}" width="100">_("Password"):</td>
                <td><input type="password" name="password" value="{$post_limit|smarty:nodefaults|default:null}" /></td>
            </tr>    
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="hidden" value="{$uri|smarty:nodefaults|default:null}" name="uri" />
                    <input type="submit" value={quote}_("Login"){/quote} />
                </td>
            </tr>
            </table>
            {/arag_form}
        {/arag_block}
    {/if}
{/arag_block}
