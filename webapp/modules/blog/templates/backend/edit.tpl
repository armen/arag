{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
{arag_form uri="blog/backend/do_edit" method="post"}
<table border="0" dir="{dir}" width="100%">
<tr>
    <td align="{right}" width="100"><span class="form_required">&nbsp;*&nbsp;</span>_("Subject"):</td>
    <td><input type="text" name="subject" value="{$subject}" /></td>
</tr>
<tr>
    <td align="{right}" width="100">_("Status"):</td>
    <td>
        {html_options options=$status name="status" selected=$published}
    </td>
</tr>
<tr>
    <td align="{right}" width="100">_("Category"):</td>
    <td>
        <select name="category">
            <option value="0">_("[No Category]")</option>
            {html_options options=$categories selected=$category}
        </select>
    </td>
</tr>
<tr>
    <td align="{right}">_("Entry Body"):</td>
    <td>{arag_rte name="entry" value=$entry|smarty:nodefaults}</td>
</tr>
<tr>
    <td align="{right}" width="100">&nbsp;</td>
    <td>
        {if $allow_comments}{assign var="allow_comments" value="checked=\"checked\""}{/if}
        {if $requires_moderation}{assign var="requires_moderation" value="checked=\"checked\""}{/if}
        <div><label><input type="checkbox" name="allow_comments" />_("Allow comments to this entry")</label></div>
        <div><label><input type="checkbox" name="requires_moderation" />_("Comments to this entry requires moderation")</label></div>
    </td>
</tr>
<tr>
    <td align="{right}">_("Extended Body"):</td>
    <td>{arag_rte name="extended_entry" value=$extended_entry|smarty:nodefaults}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td>
        <input type="hidden" name="id" value="{$id}" />    
        <input type="submit" value={quote}_("Edit"){/quote} />
    </td>
</tr>
</table>
{/arag_form}
