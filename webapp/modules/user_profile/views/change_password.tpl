{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{assign var=uri value="change_password/%section%/index"}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Password changed successfuly!")
    {/arag_block}
{/if}
{arag_block}
    {arag_form uri=$uri|replace:"%section%":$section}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Old Password"):
                </td>
                <td align="{left}">
                    <input type="password" name="oldpassword" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("New Password"):
                </td>
                <td align="{left}">
                    <input type="password" name="newpassword" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Re-New Password"):
                </td>
                <td align="{left}">
                    <input type="password" name="renewpassword" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    &nbsp;
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
