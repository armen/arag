{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{if $contact_top_template|strip != ''}
{arag_block}
    {$contact_top_template|smarty:nodefaults|default:null}
{/arag_block}
{/if}
{arag_block}
    {arag_validation_errors}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">_("Full name"):</td>
                <td align="{left}">
                    <input name="contact_name" value="{$contact_name|smarty:nodefaults|default:null}" type="text" />
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Phone Number"):</td>
                <td align="{left}">
                    <input name="contact_number" value="{$contact_number|smarty:nodefaults|default:null}" type="text" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Email Address"):{asterisk}</td>
                <td align="{left}">
                    <input type="text" name="contact_email" value="{$contact_email|smarty:nodefaults|default:null}" dir="ltr" />
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Message Title"):{asterisk}</td>
                <td align="{left}">
                    <input type="text" name="contact_title" value="{$contact_title|smarty:nodefaults|default:null}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Message Content"):{asterisk}</td>
                <td align="{left}">
                    <textarea rows="6" cols="" name="contact_content">{$contact_content|smarty:nodefaults|default:null}</textarea>
                </td>
            </tr>
            <tr>
                <td align="{right}">{asterisk}_("Type the text you see in image"):</td>
                <td align="{left}">
                    {arag_captcha}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
{if $contact_bottom_template|strip != ''}
{arag_block}
    {$contact_bottom_template|smarty:nodefaults|default:null}
{/arag_block}
{/if}
