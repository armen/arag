{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>{$page_title}</title>
    {arag_head}
</head>
<body>
    {arag_block align="right" template="blank"}
        {if $auth}
            {capture assign="welcome"}_("Welcome %s %s"){/capture}
            {capture assign="logout"}_("Logout"){/capture}
            {capture assign="profile"}_("My Profile"){/capture}
            {capture assign="controlpanel"}_("Control Panel"){/capture}        
            {$welcome|sprintf:$firstname:$surname} | 
            {html_anchor uri="user_profile/backend/index" title="$profile"} | 
            {html_anchor uri="user/frontend/logout" title="$logout"} | 
            {html_anchor uri="controlpanel" title="$controlpanel"}
        {else}
            {html_anchor uri="user/frontend/login" title="login"} |
            {html_anchor uri="user/frontend/registration" title="Register"} |
            {html_anchor uri="user/frontend/forget_password" title="Forget your password?"}
        {/if}
    {/arag_block}

    {$content|smarty:nodefaults|default:""}
    {literal}
        Execution: {execution_time} Memory usage: {memory_usage}
    {/literal}    
</body>
</html>
