{arag_plist name="google_api_keys"}

{arag_validation_errors}

{arag_block template="collapsible" title=_("Add")}
    {arag_form uri="locations/backend/google_api_keys" method="post"}
        <table dir="{dir}">
            <tr>
                <td>
                    _("Domain:")
                </td>
                <td>
                    <input type="text" name="domain" value="{$domain}" />
                </td>
            </tr>
            <tr>
                <td>
                    _("Google API key:")
                </td>
                <td>
                    <input type="text" name="google_api_key" value="{$google_api_key}" />
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}