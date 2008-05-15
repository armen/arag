{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{arag_block}
    
    {arag_validation_errors}

    {arag_form uri="report_generator/backend/generate_report" method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td align="{right}" width="70">_("Table Name"):</td>
        <td>{html_options name="table_name" options=$allowed_tables selected=$table_name|smarty:nodefaults|default:null}</td>
    </tr>
    <tr>
        <td align="{right}" width="70">_("Name"):</td>
        <td><input type="text" name="report_name" value="{$report_label|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}">_("Description"):</td>
        <td><textarea name="report_description" rows="10" cols="20">{$report_description|smarty:nodefaults|default:null}</textarea></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value={quote}_("Next"){/quote} />
        </td>
    </tr>
    </table>

    {/arag_form}
{/arag_block}
