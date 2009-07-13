{arag_validation_errors}

{arag_block}
    {arag_form method="post" uri=locations/backend/add/`$parent`}
        <table width="30%" dir="{dir}">
            <tr>
                <td>
                    _("English name"):
                </td>
                <td>
                    <input type="text" name="english" value="{$english}" />
                </td>
            </tr>
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
                    _("Code"):
                </td>
                <td>
                    <input type="text" name="code" value="{$code}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Type"):
                </td>
                <td>
                    <select name="type">
                        {foreach from=$types key='type' item='name'}
                            <option value="{$type}">{$name}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    _("Latitude"):
                </td>
                <td>
                    <input type="text" name="latitude" value="{$latitude}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Longitude"):
                </td>
                <td>
                    <input type="text" name="longitude" value="{$longitude}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Submit"):
                </td>
                <td>
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}