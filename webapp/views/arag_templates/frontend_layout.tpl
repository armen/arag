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
        {if $auth}
            {capture assign="welcome"}_("Welcome %s %s"){/capture}
            {$welcome|sprintf:$name:$lastname} | 
            {html_anchor uri="user/frontend/logout" title="logout"}
        {else}
            {html_anchor uri="user/frontend/login" title="login"}
        {/if}
    {/arag_block}

    {$content|smarty:nodefaults}
    {literal}
        Execution: {execution_time} Memory usage: {memory_usage}
    {/literal}    
</body>
</html>
