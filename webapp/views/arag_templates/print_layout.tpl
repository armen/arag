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
    <link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}styles/print.css" />
</head>
<body onload="javascript:window.print();">
    {$content|smarty:nodefaults|default:""}
</body>
</html>
