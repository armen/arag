<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{literal}{#EasyUpload.Title}{/literal}</title>
	<script language="javascript" type="text/javascript" src="{$arag_base_url|smarty:nodefaults}modpub/tinymce/tiny_mce_popup.js"></script>
	<base target="_self" />
	{literal}
	<script type="text/javascript">
		var SetName = {
			init : function(ed) {
				Name = ed.editorId;
				document.getElementById( 'Name' ).value = Name;
			}
		};

		tinyMCEPopup.onInit.add(SetName.init, SetName);
	</script>
	{/literal}
</head>
<body>
	<div align="center">
		<div class="title">{literal}{#EasyUpload.Title}{/literal}:<br /><br /></div>
		{arag_form uri="tinymce/frontend" method="POST" enctype="multipart/form-data"}
			<label for="File">
				{literal}{#EasyUpload.SelectFile}{/literal}:
			</label>
			<input name="file" type="file" />
			<input id="Name" name="name" type="hidden" />
			<br /><br />
			<input type="submit" name="Save" value="{literal}{#EasyUpload.Save}{/literal}" />
		{/arag_form}
	</div>
</body>
</html>