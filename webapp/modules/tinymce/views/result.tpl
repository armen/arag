<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script language="javascript" type="text/javascript" src="{$arag_base_url|smarty:nodefaults}modpub/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript">
		var ed = tinyMCEPopup.editor;
        var text = "_("Click To Enlarge")";
	    {literal}
        {/literal}{if !$thumb}{literal}
            window.parent.ed.execCommand('mceInsertContent', false, window.parent.ed.dom.createHTML('img', {
                src:"{/literal}{$url}{literal}"
            }));
        {/literal}{else}{literal}
            window.parent.ed.execCommand('mceInsertContent', false, window.parent.ed.dom.createHTML('a', {href:"{/literal}{$url}{literal}", target: "_blank"},
                                                                    window.parent.ed.dom.createHTML('img', {src:"{/literal}{$url_thumb}{literal}", alt: text, title: text})));
        {/literal}{/if}{literal}
		window.close();
	</script>
	{/literal}
</head>
<body>
</body>
</html>
