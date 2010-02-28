{arag_block}
    {arag_form uri="poll_manager/backend/list/add"}
        <table width="100%" border="0" cellpadding="0" cellspacing="0" dir="{dir}">
            <tr>
                <td align="{left}" width="70">
                    _("Title")
                </td>
                <td align="{left}">
                    <input type="text" name="title" value="{$title|smarty:nodefaults|default:null}" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td align="{left}" valign="top">
                    _("Quiz")
                </td>
                <td align="{left}">
                    <textarea name="quiz">{$quiz|smarty:nodefaults|default:null}</textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td align="{left}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Add"){/quote} />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
