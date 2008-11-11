{*Smarty*}
{arag_tabbed_block name="global_tabs"}
{arag_block}
    {arag_validation_errors}
    {arag_form uri='messaging/frontend/inbox/new_message' method='post'}
        {arag_block template="info"}
            {capture assign="info_msg"}
                _("Fields marked with a %s are required.")
            {/capture}
            {asterisk message=$info_msg}
        {/arag_block}
        <table >
            <tr>
                <td>{asterisk}_("To"):</td>
                <td>
                    <select name="username">
                    <option value="">&nbsp;</option>
                    {html_options options=$user_options}
                    </select>
                </td>
            </tr>
            <tr>
                <td>{asterisk}_("Subject"):</td>
                <td><input type="text" name="subject" size="15" /></td>
            </tr>
            <tr>
                <td>_("Message body"):</td>
                <td>{arag_rte name="body"}</td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" value={quote}_("Send"){/quote} />
                    <input type="reset" value={quote}_("Reset"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
{/arag_tabbed_block}