{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}

    {arag_block align="left" template="tips"}
        _("To have a dynamic template which has replaceable items, you should use %variableName% like the following example template in which %username% will be replaced by username variable in the application:")<br />
        _("Your username is %username%.")<br />
        _("Avialable variables are %username%, %password%, %appname% and %uri%")
    {/arag_block}

    {arag_validation_errors}

    {if $saved}
        {arag_block align="left" template="info"}
            _("Settings are updated successfuly!")
        {/arag_block}
    {/if}

    {arag_form uri="user/backend/applications/email_template" method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td align="{right}" width="100">_("Subject"):{asterisk}</td>
        <td><input type="text" name="subject" value="{$subject|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="100" valign="top">_("Email Template"):{asterisk}</td>
        <td><textarea name="template" rows="7" cols="15">{$template|smarty:nodefaults|default:null}</textarea></td>
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
