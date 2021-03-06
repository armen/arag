{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_block}
    {if $message}
        {arag_block template="info"}
            {$message}
        {/arag_block}
    {/if}
    <table border="0" dir="{dir}">
        <tr>
            <td align="{right}">_("Full name"):</td>
            <td align="{left}">{$contact_info.name}</td>
        </tr>
        <tr>
            <td align="{right}">_("Phone Number"):</td>
            <td align="{left}">{$contact_info.tel}</td>
        </tr>
        <tr>
            <td align="{right}">_("Email Address"):</td>
            <td align="{left}">{$contact_info.email}</td>
        </tr>
        <tr>
            <td align="{right}">_("Create Date"):</td>
            <td align="{left}">{$contact_info.create_date}</td>
        </tr>
        <tr>
            <td align="{right}">_("Message Title"):</td>
            <td align="{left}">{$contact_info.title}</td>
        </tr>
        <tr>
            <td align="{right}" valign="top">_("Message Content"):</td>
            <td align="{left}">
                {$contact_info.content|nl2br}
            </td>
        </tr>
    </table>
{/arag_block}
