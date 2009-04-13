{arag_block}
    {arag_validation_errors}

    {arag_form uri=calendar/backend/edit/$id method='post'}
        <table dir="{dir}">
            <tr>
                <td>
                    _("Date"):
                </td>
                <td>
                    {arag_date name='date' value=$date}
                </td>
            </tr>
            <tr>
                <td>
                    _("Description"):
                </td>
                <td>
                    {arag_rte name='description' value=$description}
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    <input name="submit" type="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}