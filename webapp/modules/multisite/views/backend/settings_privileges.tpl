{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}

    {arag_validation_errors}

    {if $saved}
        {arag_block align="left" template="info"}
            _("Settings are updated successfuly!")
        {/arag_block}
    {/if}

    {arag_form uri="multisite/backend/settings/privileges" method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td align="{right}" width="250" valign="top">_("Set default privileges for Admin groups"):</td>
        <td><textarea name="adminpri" rows="7" cols="15">{$adminpri|smarty:nodefaults|default:null}</textarea></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td align="{right}" width="250" valign="top">_("Set default privileges for Anonymous groups"):</td>
        <td><textarea name="anonypri" rows="7" cols="15">{$anonypri|smarty:nodefaults|default:null}</textarea></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value={quote}_("Save"){/quote} />
            <input type="reset" value={quote}_("Reset"){/quote} />
        </td>
    </tr>
    </table>
    {/arag_form}

{/arag_block}
