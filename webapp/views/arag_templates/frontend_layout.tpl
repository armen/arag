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
</head>
<body>
    {arag_block align="right" template="blank"}        
        {html_anchor uri="user/frontend/login" title="login"}
    {/arag_block}

    {$content|smarty:nodefaults}
</body>
</html>
