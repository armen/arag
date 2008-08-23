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
<script language="javascript" type="text/javascript" src="{$arag_base_url|smarty:nodefaults}modpub/tinymce/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
{literal}
<script language="javascript" type="text/javascript">
tinyMCE.init({
        mode: "specific_textareas",
        editor_selector: "rte"
});
</script>
{/literal}