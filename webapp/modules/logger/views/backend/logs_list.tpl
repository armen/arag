{*smarty*}
{arag_validation_errors}
{arag_block}
    {arag_form uri="logger/backend" method="post"}
        <table border="0">
            <tr>
                <td align="{right}">_("Archive atatus"):</td>
                <td>
                   {html_options name="archive_status" options=$archive selected=$archive_status|smarty:nodefaults|default:''}
                </td>
            </tr>
            <tr>
                <td align="{right}">_("User"):</td>
                <td><input type="text" name="username" value="{$username|smarty:nodefaults}" /></td>
            </tr>
            <tr>
                <td align="{right}">_("Operation"):</td>
                <td>
                    <select name="operation">
                        <option value="">&nbsp;</option>
                        {html_options options=$massages selected=$operation|smarty:nodefaults|default:''}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Date From"):</td>
                <td>{arag_date name="from" value=$from|smarty:nodefaults|default:''}</td>
            </tr>
            <tr>
                <td align="{right}">_("Date To"):</td>
                <td>{arag_date name="to" value=$to|smarty:nodefaults|default:''}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value={quote}_("Search"){/quote} /></td>
            </tr>
       </table>
    {/arag_form}
{/arag_block}
{arag_block}
    {arag_plist}
{/arag_block}
