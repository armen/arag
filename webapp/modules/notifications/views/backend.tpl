{*Smarty*}
{arag_block}
    {arag_form uri="notifications/backend/index/$type" method="post"}
        {arag_validation_errors}
        {if $error|smarty:nodefaults}
            {arag_block template="error"}
                {$error}
            {/arag_block}
        {/if}
        {arag_block template="info"}
            {if $message|smarty:nodefaults}
                {$message}
            {/if}
            {capture assign="info_msg"}
                _("Fields marked with a %s are required.")
            {/capture}
            {asterisk message=$info_msg}
        {/arag_block}
        <table >
            <tr>
                <td>_("To"):</td>
                <td>
                {if $type == 'sms'}
                    {arag_user_search name="to"}
                {else}
                    <input name="to" type="text" size="15" />
                {/if}
                </td>
            </tr>
            {if $type == 'email'}
            <tr>
                <td>{asterisk}_("Subject"):</td>
                <td><input type="text" name="subject" size="15" /></td>
            </tr>
            {/if}
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
