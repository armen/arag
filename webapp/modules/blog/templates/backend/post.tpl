{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
{arag_form uri="blog/backend/do_post" method="post"}
<table border="0" dir="{dir}" width="100%">
<tr>
    <td align="{right}" width="100"><span class="form_required">&nbsp;*&nbsp;</span>_("Subject"):</td>
    <td><input type="text" name="subject" /></td>
</tr>
<tr>
    <td align="{right}" width="100">_("Status"):</td>
    <td>
        {html_options options=$status name="status"}    
    </td>
</tr>
<tr>
    <td align="{right}" width="100">_("Category"):</td>
    <td>
        <select name="category">
            <option value="0">_("[No Category]")</option>
            {html_options options=$categories}
        </select>
    </td>
</tr>
<tr>
    <td align="{right}">_("Entry Body"):</td>
    <td>{arag_rte name="entry"}</td>
</tr>
<tr>
    <td align="{right}" width="100">&nbsp;</td>
    <td>
        <div><label><input type="checkbox" name="allow_comments" />_("Allow comments to this entry")</label></div>
        <div><label><input type="checkbox" name="requires_moderation" />_("Comments to this entry requires moderation")</label></div>
    </td>
</tr>
<tr>
    <td align="{right}">_("Extended Body"):</td>
    <td>{arag_rte name="extended_entry"}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td>
        <input type="submit" value={quote}_("Create"){/quote} />
    </td>
</tr>
</table>
{/arag_form}
