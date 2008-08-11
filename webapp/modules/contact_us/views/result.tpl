{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{if $result == true}
    {arag_block template="info"}
        _("Your message has been sent successfully .")
    {/arag_block}
{else}
    {arag_block template="error"}
        _("Your message failed!")
    {/arag_block}
{/if}
