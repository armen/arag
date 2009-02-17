{arag_block}
    {arag_validation_errors}

    {arag_form uri="user/backend/applications/add"}
        <table dir="{dir}">
            <tr>
                <td>
                    _("Name"):
                </td>
                <td>
                    <input type="text" name="name" value="{$name}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Clone data from"):
                </td>
                <td>
                    <select name="template">
                        <option value="">--</option>
                        {foreach from=$applications item='application'}
                            <option value="{$application.name}">{$application.name}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="save" value={quote}_("Submit"){/quote} />
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}