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
        {capture assign="welcome"}_("Welcome %s %s"){/capture}
        {capture assign="logout"}_("Logout"){/capture}
        {capture assign="profile"}_("My Profile"){/capture}
        {capture assign="controlpanel"}_("Control Panel"){/capture}
        {$welcome|sprintf:$firstname:$surname} | 
        {helper function="html::anchor" uri="user_profile/backend/index" title="$profile"} | 
        {helper function="html::anchor" uri="user/frontend/logout" title="$logout"} | 
        {helper function="html::anchor" uri="controlpanel" title="$controlpanel"}
    {/arag_block}            

    {arag_tabbed_block name="global_tabs"}
        {$content|smarty:nodefaults|default:""}
    {/arag_tabbed_block}
    {literal}
        Execution: {execution_time} Memory usage: {memory_usage}
    {/literal}
</body>
</html>
