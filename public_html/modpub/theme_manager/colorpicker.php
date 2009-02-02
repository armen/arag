<html>
<head>
<title>Color Picker</title>
<?php
    $path = pathinfo($_SERVER['SCRIPT_NAME']);
    $path = $path['dirname'];
?>
<script language="javascript" src="<?php echo $path; ?>/js/colorpicker/color_functions.js" type="text/javascript"></script>
<script language="javascript" src="<?php echo $path; ?>/js/colorpicker/js_color_picker_v2.js" type="text/javascript"></script>
<link href="<?php echo $path; ?>/css/js_color_picker_v2.css" rel="stylesheet" type="text/css" />
</head>
<body dir="ltr">
<?php $targetId = $_GET['targetId'] ?>
<script>
showColorPicker(window.opener.document.getElementById('<?php echo $targetId ?>'),window.opener.document.getElementById('<?php echo $targetId; ?>'));
</script>
</body>
</html>
