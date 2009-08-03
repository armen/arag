{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{foreach name="messengers" from=$messengers item=messenger}
    {if $smarty.foreach.messengers.first}
        <table border="0" dir="{dir}">
    {/if}
        <tr>
            <td><a href="{$messenger.href}"><img border="0" src="{$messenger.image}" /></a></td>
            <td><a href="{$messenger.href}">{$messenger.subject}</a></td>
        </tr>
    {if $smarty.foreach.messengers.last}
        </table>
    {/if}
{/foreach}
