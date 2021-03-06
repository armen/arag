{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_block}
    {arag_block align="left" template="error"}
        <div><h2>_("Your request is invalid!")</h2></div>
        {if $message}
            <div><b>_("Reason:")&nbsp;{$message}</b></div>
        {/if}
        <div>_("If your are sure that your request isn't invalid then contact your website administrator.")</div>
        <div><a href="{kohana_helper function="url::site" uri=$uri}">_("Return to the Main Page")</a></div>
    {/arag_block}
{/arag_block}
