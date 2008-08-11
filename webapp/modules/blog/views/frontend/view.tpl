{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_plist name="entry" template="list/blog.tpl"}

{if isset($extended|smarty:nodefaults) && $extended}
    {arag_comment name="comments"}
{/if}
