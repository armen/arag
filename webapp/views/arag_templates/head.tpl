{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="{$arag_base_url|smarty:nodefaults}images/misc/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="{$arag_base_url|smarty:nodefaults}images/misc/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" media="all" type="text/css" title="Arag" href="{$arag_base_url|smarty:nodefaults}styles/styles.css" />
<link rel="stylesheet" media="all" type="text/css" title="Arag" href="{$arag_base_url|smarty:nodefaults}modpub/{$arag_current_module}/styles.css" />
<link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}scripts/JalaliJSCalendar/skins/calendar-blue.css" />
<script language="javascript" type="text/javascript" src="{$arag_base_url|smarty:nodefaults}modpub/tinymce/tiny_mce.js"></script>
{literal}
<script language="javascript" type="text/javascript">
tinyMCE.init({
        theme : "advanced",
        plugins : "easyUpload, table",
        theme_advanced_buttons3_add : "easyUpload, table, delete_table, delete_col, col_after, col_before, cell_props, delete_row, row_after, row_before, row_props, split_cells, merge_cells",
        mode: "specific_textareas",
        editor_selector: "rte"
});
</script>
{/literal}