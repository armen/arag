{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}

{arag_validation_errors}

{arag_block}
    {if !$show_form}
        {arag_block template="error"}
            {foreach from=$messages item=message}
                {$message|smarty:nodefaults}<br />
            {/foreach}
        {/arag_block}
    {else}
        {arag_form uri='multisite/backend/install/index'}
            <table border="0" dir="{dir}">
                <tr>
                    <td align="{right}">
                        _("Application name"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="appname" value="{$appname|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Email"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="email" value="{$email|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Retype Email"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="reemail" value="{$reemail|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td align="{right}" valign="top">
                        _("Modules"):{asterisk}
                    </td>
                    <td align="{left}">
                        {foreach from=$modules item=module}
                            <label><input type="checkbox" name="modules[]" value="{$module.module|smarty:nodefaults|default:null}" />
                                    &nbsp;{$module.name}</label><br />
                        {/foreach}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                    </td>
                    <td align="{left}">
                        <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                    </td>
                </tr>
            </table>
        {/arag_form}
    {/if}
{/arag_block}
