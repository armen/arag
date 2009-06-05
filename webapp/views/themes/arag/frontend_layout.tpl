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
            {capture assign="current_help_uri"}{kohana_helper function="url::current"}{/capture}
            {assign var='current_help_uri' value=$current_help_uri|replace:'/':'|'}
            {capture assign="logout"}_("Logout"){/capture}
            {capture assign="profile"}_("My Profile"){/capture}
            {capture assign="controlpanel"}_("Control Panel"){/capture}
            {capture assign="inbox"}_("Inbox (%d)"){/capture}
            {capture assign="add_help"}_("Add Help"){/capture}
            {capture assign="manage_help"}_("Manage Helps"){/capture}
            {kohana_helper function="html::anchor" uri="user/frontend/logout" title=$logout} |
            {kohana_helper function="html::anchor" uri="user_profile/frontend/index" title=$profile} |
            {kohana_helper function="html::anchor" uri="messaging/frontend/inbox" title=$inbox|sprintf:$messages_count} |
            {kohana_helper function="html::anchor" uri="controlpanel" title=$controlpanel} |
            {arag_is_accessible uri="/help/backend"}
                {kohana_helper function="html::anchor" uri="help/backend/add/`$current_help_uri`" title=$add_help} |
                {kohana_helper function="html::anchor" uri="help/backend/listing/`$current_help_uri`" title=$manage_help} |
            {/arag_is_accessible}
        {else}
            {capture assign="login"}_("Login"){/capture}
            {capture assign="forgot"}_("Forget your password?"){/capture}
            {capture assign="register"}_("Register"){/capture}
            {kohana_helper function="html::anchor" uri="user/frontend/login" title=$login} |
            {kohana_helper function="html::anchor" uri="user/frontend/registration" title=$register} |
            {kohana_helper function="html::anchor" uri="user/frontend/forget_password" title=$forgot} |
        {/if}
        <a href="{kohana_helper function="url::site"}">_("Home")</a>
    {/arag_block}
    {arag_block align="right" template="blank"}
    {if $auth}
        {capture assign="welcome"}_("Welcome %s %s - %s"){/capture}
        {kohana_helper function="html::anchor" uri="user_profile/frontend/index" title=$welcome|sprintf:$firstname:$surname:$arag_username}
        <div align="{right}">{arag_user_credit}</div>
    {/if}
    {/arag_block}

    {arag_help_messages}
    {$content_wrapper|smarty:nodefaults|default:""}
    {literal}
        Execution: <b>{execution_time}</b> Memory usage: <b>{memory_usage}</b> Included files: <b>{included_files}</b>
    {/literal}
</body>
</html>
