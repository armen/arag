{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{if $show_headers|smarty:nodefaults|default:null}
    {show_arag_headers}
{/if}
{$content|smarty:nodefaults|default:""}
