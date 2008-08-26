<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script language="javascript" type="text/javascript" src="{$arag_base_url|smarty:nodefaults}modpub/tinymce/tiny_mce_popup.js"></script>
	{literal}
	<script type="text/javascript">
		var ed = tinyMCEPopup.editor;
		window.parent.ed.execCommand('mceInsertContent', false, window.parent.ed.dom.createHTML('img', {
			src:"{/literal}{$url}{literal}"
		}));
		window.close();
	</script>
	{/literal}
</head>
<body>
</body>
</html>