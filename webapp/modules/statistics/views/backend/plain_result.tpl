{arag_block}
    {arag_form uri="statistics/backend/index" method="post"}
        <table align="{dir}">
            <tr>
                <td>
                    _("From")
                </td>
                <td>
                    {arag_date name="from" value=$from|default:null}
                </td>
            </tr>
            <tr>
                <td>
                    _("To")
                </td>
                <td>
                    {arag_date name="to" value=$to|default:null}
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

{foreach from=$plugins item='plugin'}
    {arag_block title=$plugin->title() template='default'}
        <table align"{dir}">
            {foreach from=$plugin->series() key='name' item='title'}
                <tr>
                    <td>
                        {$title}:
                    </td>
                    <td>
                        {$plugin->data.$name}
                    </td>
                </tr>
            {/foreach}
        </table>
    {/arag_block}
{/foreach}