{arag_validation_errors}

{arag_block}
    {arag_form method="post" uri=locations/backend/edit/`$location.id`}
        <table width="30%" dir="{dir}">
            <tr>
                <td>
                    _("English name"):
                </td>
                <td>
                    <input type="text" name="english" value="{$location.english}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Name"):
                </td>
                <td>
                    <input type="text" name="name" value="{$location.name}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Code"):
                </td>
                <td>
                    <input type="text" name="code" value="{$location.code}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Type"):
                </td>
                <td>
                    <select name="type">
                        {foreach from=$types key='type' item='name'}
                            <option value="{$type}" {if $location.type == $type}selected="selected"{/if}>
                                {$name}
                            </option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    _("Latitude"):
                </td>
                <td>
                    <input type="text" name="latitude" value="{$location.latitude}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Longitude"):
                </td>
                <td>
                    <input type="text" name="longitude" value="{$location.longitude}" />
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