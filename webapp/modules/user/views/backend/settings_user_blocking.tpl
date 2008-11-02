{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}

    {arag_block align="left" template="tips"}
        _("0 for expire time means unlimited")<br />
        _("Number of attempts to displaying captcha should be less than number of attempts to block a user")<br />
        _("Remember, blocking and displaying captcha will be disabled if you set blocking attempts to 0")<br />
    {/arag_block}

    {arag_validation_errors}

    {if $saved}
        {arag_block align="left" template="info"}
            _("Settings are updated successfuly!")
        {/arag_block}
    {/if}

    {arag_form uri="user/backend/applications/user_blocking" method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td align="{right}" width="300">_("Number of login attempts before blocking"):</td>
        <td><input type="text" name="block_counter" value="{$block_counter|smarty:nodefaults|default:null}" dir="ltr" /></td>
    </tr>
    <tr>
        <td align="{right}" width="300">_("Number of login attempts before displaying captcha"):</td>
        <td><input type="text" name="captcha_counter" value="{$captcha_counter|smarty:nodefaults|default:null}" dir="ltr" /></td>
    </tr>
    <tr>
        <td align="{right}" width="300">_("Blocking duration (hours)"):</td>
        <td><input type="text" name="block_expire" value="{$block_expire|smarty:nodefaults|default:null}" dir="ltr" /></td>
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
