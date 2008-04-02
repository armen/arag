{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{arag_block}
    {arag_block align="left" template="error"}
        <div><h3>_("You are not authorized to access this section!")</h3></div>
        <div><a href="{url_site}">_("Return to the Main Page")</a></div>
    {/arag_block}
{/arag_block}
{if $show_login}
    {arag_block}
        {arag_block template="info"}
            _("You must login to continue.")
        {/arag_block}
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
{/if}
