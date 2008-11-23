{*smarty*}
{arag_tabbed_block name="global_tabs"}
    {arag_block}
        <table border="0">
            <tr>
                <td>_("Subject"):</td>
                <td>{$subject}</td>
            </tr>
            <tr>
                <td>_("Date"):</td>
                <td>{kohana_helper function="format::date" date=$created_date}</td>
            </tr>
            <tr>
                <td>_("From"):</td>
                <td>{$message_from}</td>
            </tr>
            <tr>
                <td>_("To"):</td>
                <td>{$message_to}</td>
            </tr>
        </table>
    {/arag_block}
    {arag_block}
        {arag_block template="blank"}
            <table >
                <tr>
                    <td>{$body}</td>
                </tr>
            </table>
        {/arag_block}
    {/arag_block}
{/arag_tabbed_block}
