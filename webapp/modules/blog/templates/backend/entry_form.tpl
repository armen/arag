{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
<table border="0" dir="{dir}" width="100%">
<tr>
    <td align="{right}" width="100">{asterisk}_("Subject"):</td>
    <td><input type="text" name="subject" value="{$subject|smarty:nodefaults|default:null}" /></td>
</tr>
<tr>
    <td align="{right}" width="100">_("Status"):</td>
    <td>
        {html_options options=$status_list name="status" selected=$published|smarty:nodefaults|default:1}
    </td>
</tr>
<tr>
    <td align="{right}" width="100">_("Category"):</td>
    <td>
        <select name="category">
            <option value="0">_("[No Category]")</option>
            {html_options options=$categories selected=$category|smarty:nodefaults|default:null}
        </select>
    </td>
</tr>
<tr>
    <td align="{right}">{asterisk}_("Entry Body"):</td>
    <td>{arag_rte name="entry" value=$entry|smarty:nodefaults|default:null}</td>
</tr>
<tr>
    <td align="{right}" width="100">&nbsp;</td>
    <td>
        <div><label>
            <input type="checkbox" name="allow_comments" value="1" {html_checked name="allow_comments"}/>_("Allow comments to this entry")
        </label></div>
        <div><label>
            <input type="checkbox" name="requires_moderation" value="1" {html_checked name="requires_moderation"}/>
            _("Comments to this entry requires moderation")
        </label></div>
    </td>
</tr>
<tr>
    <td align="{right}">_("Extended Body"):</td>
    <td>{arag_rte name="extended_entry" value=$extended_entry|smarty:nodefaults|default:null}</td>
</tr>
