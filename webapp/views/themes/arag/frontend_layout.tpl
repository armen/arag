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
	<link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}themes/arag/styles/style.css" />
    <link rel="stylesheet" media="all" type="text/css" href="{kohana_helper function="url::site" uri="theme_manager/styles.css"}" />
</head>
<body>
    {arag_block align="right" template="blank"}
        {if $auth}
            {capture assign="welcome"}_("Welcome %s %s (%s)"){/capture}
            {capture assign="logout"}_("Logout"){/capture}
            {capture assign="profile"}_("My Profile"){/capture}
            {capture assign="controlpanel"}_("Control Panel"){/capture}
            {$welcome|sprintf:$firstname:$surname:$arag_username} |
            {kohana_helper function="html::anchor" uri="user_profile/backend/index" title=$profile} |
            {kohana_helper function="html::anchor" uri="user/frontend/logout" title=$logout} |
            {kohana_helper function="html::anchor" uri="controlpanel" title=$controlpanel}
        {else}
            {kohana_helper function="html::anchor" uri="user/frontend/login" title="login"} |
            {kohana_helper function="html::anchor" uri="user/frontend/registration" title="Register"} |
            {kohana_helper function="html::anchor" uri="user/frontend/forget_password" title="Forget your password?"}
        {/if}
    {/arag_block}

    {arag_tabbed_block name="global_tabs"}
        {$content|smarty:nodefaults|default:""}
    {/arag_tabbed_block}
    {literal}
        Execution: <b>{execution_time}</b> Memory usage: <b>{memory_usage}</b> Included files: <b>{included_files}</b>
    {/literal}
</body>
</html>
