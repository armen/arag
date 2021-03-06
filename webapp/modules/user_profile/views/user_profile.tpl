{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{assign var=uri value="user_profile/%section%/index"}
{arag_validation_errors}
{if !$isset_profile}
    {arag_block align="left" template="warning"}
        _("You hadn't enter your personal information yet")
    {/arag_block}
{/if}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Profile edited successfuly!")
    {/arag_block}
{else}
    {arag_block}
        {arag_form uri=$uri|replace:"%section%":$section}
            <table border="0" dir="{dir}">
                <tr>
                    <td align="{right}">
                        _("Username"):
                    </td>
                    <td align="{left}">
                        {$username|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Name"):
                    </td>
                    <td align="{left}">
                        {$name|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Last Name"):
                    </td>
                    <td align="{left}">
                        {$lastname|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Email"):
                    </td>
                    <td align="{left}">
                        {$email|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Phone"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="phone" value="{$phone|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Cellphone"):
                    </td>
                    <td align="{left}">
                        <input type="text" name="cellphone" value="{$cellphone|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">{asterisk}_("Country/Province/City"):</td>
                    <td align="{left}">{arag_location name='location' value=$location|smarty:nodefaults|default:null}</td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Address"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="address" value="{$address|smarty:nodefaults|default:null}" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Postal Code"):
                    </td>
                    <td align="{left}">
                        <input type="text" name="postal_code" value="{$postal_code|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
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
{/if}
