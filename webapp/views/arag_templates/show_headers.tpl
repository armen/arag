{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{foreach item='header' from=$headers|smarty:nodefaults}
    {$header|smarty:nodefaults}
{/foreach}
