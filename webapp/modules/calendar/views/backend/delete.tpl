{arag_block align="left"}
    {arag_block align="left" template="warning"}
    <table border="0" dir="{dir}">
    <tr>
        <td>
            _("Do you really want to delete the selected Entry?")
        </td>
        <td>
            {arag_form uri=calendar/backend/delete/`$holiday.id` method="post"}
                <input type="submit" value={quote}_("Yes"){/quote} />
            {/arag_form}
        </td>
        <td>
            {arag_form uri=calendar/backend/index method="get"}
                <input type="submit" value={quote}_("No"){/quote} />
            {/arag_form}
        </td>
    </tr>
    </table>
    {/arag_block}
{/arag_block}
