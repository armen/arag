{arag_block}
    {arag_block template="info"}
        _("Poll updated successfuly")
    {/arag_block}
    {arag_form uri="poll_manager/backend/list/edit"}
        <table width="100%" border="0" cellpadding="0" cellspacing="0" dir="{dir}">
            <tr>
                <td align="{left}" width="70">
                    _("Title")
                </td>
                <td align="{left}">
                    <input type="text" name="title" value="{$poll->title|smarty:nodefaults|default:null}" />
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
                    <textarea name="quiz">{$poll->quiz|smarty:nodefaults|default:null}</textarea>
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
                    <input type="hidden" value="{$poll->id}" name="id" />
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
{capture assign="block_title"}_("Choices"){/capture}
{arag_block title=$block_title}
    {arag_form uri="poll_manager/backend/list/add_choice"}
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
                <td align="{left}" width="70">
                    _("Color")
                </td>
                <td align="{left}">
                    {arag_colorpicker name="color"}
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
                    <input type="hidden" value="{$poll->id}" name="poll_id" />
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
    {arag_plist name="choices"}
{/arag_block}
