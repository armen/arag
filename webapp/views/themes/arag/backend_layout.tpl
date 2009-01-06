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
        {capture assign="welcome"}_("Welcome %s %s"){/capture}
        {capture assign="logout"}_("Logout"){/capture}
        {capture assign="profile"}_("My Profile"){/capture}
        {capture assign="controlpanel"}_("Control Panel"){/capture}
        {capture assign="home"}_("Home"){/capture}
        {capture assign="home_url_site"}{kohana_helper function="url::site"}{/capture}
        {$welcome|sprintf:$firstname:$surname} |
        {kohana_helper function="html::anchor" uri="user_profile/backend/index" title="$profile"} |
        {kohana_helper function="html::anchor" uri="user/frontend/logout" title="$logout"} |
        {arag_is_accessible uri="controlpanel"}{kohana_helper function="html::anchor" uri="controlpanel" title="$controlpanel"} |{/arag_is_accessible}
        {kohana_helper function="html::anchor" uri="$home_url_site" title="$home"}
    {/arag_block}

    {arag_tabbed_block name="global_tabs"}
        {$content|smarty:nodefaults|default:""}
    {/arag_tabbed_block}
    {literal}
        Execution: <b>{execution_time}</b> Memory usage: <b>{memory_usage}</b> Included files: <b>{included_files}</b>
    {/literal}
</body>
</html>
