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
    <script language="javascript" type="text/javascript" src="{$arag_base_url|smarty:nodefaults}modpub/tinymce/tiny_mce.js"></script>
    {literal}
    <script language="javascript" type="text/javascript">
    tinyMCE.init({
            theme : "advanced",
            plugins : "easyUpload, table, layer, directionality",
            theme_advanced_buttons3_add : "easyUpload, table, delete_table, delete_col, col_after, col_before, cell_props, delete_row, row_after, row_before, row_props, split_cells, merge_cells, moveforward, movebackward, absolute, insertlayer, ltr, rtl, fontselect, fontsizeselect, forecolorpicker, backcolorpicker",
            mode: "specific_textareas",
            editor_selector: "rte",
            content_css: "{/literal}{$arag_base_url|smarty:nodefaults}modpub/tinymce/tinymce.css"
    });
    </script>

</head>
<body>
    {arag_block align="right" template="blank"}
        {if $auth}
            {capture assign="logout"}_("Logout"){/capture}
            {capture assign="profile"}_("My Profile"){/capture}
            {capture assign="controlpanel"}_("Control Panel"){/capture}
            {capture assign="inbox"}_("Inbox (%d)"){/capture}
            {kohana_helper function="html::anchor" uri="user/frontend/logout" title=$logout} |
            {kohana_helper function="html::anchor" uri="user_profile/frontend/index" title=$profile} |
            {kohana_helper function="html::anchor" uri="messaging/frontend/inbox" title=$inbox|sprintf:$messages_count} |
            {kohana_helper function="html::anchor" uri="controlpanel" title=$controlpanel} |
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
    {/if}
    {/arag_block}

    {$content_wrapper|smarty:nodefaults|default:""}
    {literal}
        Execution: <b>{execution_time}</b> Memory usage: <b>{memory_usage}</b> Included files: <b>{included_files}</b>
    {/literal}
</body>
</html>
