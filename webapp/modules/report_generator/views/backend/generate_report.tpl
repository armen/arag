{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{if empty($selected_columns|smarty:nodefaults)}
    {assign var="columns_block_status" value="collapsed"}
{else}
    {assign var="columns_block_status" value="expanded"}
{/if}

{if empty($additional_columns|smarty:nodefaults)}
    {assign var="additional_columns_block_status" value="collapsed"}
{else}
    {assign var="additional_columns_block_status" value="expanded"}
{/if}

{if empty($filters|smarty:nodefaults)}
    {assign var="filters_block_status" value="collapsed"}
{else}
    {assign var="filters_block_status" value="expanded"}
{/if}

{arag_load_script src="scripts/mootools/core.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="modpub/report_generator/complex_filters.js"}
{arag_load_script src="modpub/report_generator/additional_columns.js"}
{literal}
<script type="text/javascript">
    window.addEvent('domready', function() {
        $('save').addEvent('click', function(e) {

            var form   = $('form');
            var action = form.getProperty('action');
            form.setProperty('action', action.replace(/(.*\/)[a-z_]*$/, '$1save_report'));
            form.submit();
        });
    });
</script>
{/literal}

{arag_block template="blank"}
    {arag_form uri="report_generator/backend/generate_report" id="form"}

        {arag_block template="collapsible" title="Columns" status=$columns_block_status}
            {html_checkboxes name="columns" options=$columns selected=$selected_columns}
        {/arag_block}

        {arag_block template="collapsible" title="Additional columns" status=$additional_columns_block_status}

            {arag_validation_errors}

            {if isset($formula_splited_input|smarty:nodefaults)}
                {arag_block align="left" dir="ltr" template="info"}
                    {$formula_splited_input.first_part}
                    <span style='color:red;'><b>{$formula_splited_input.unknown_part|default:"&nbsp;"}</b></span>
                    {$formula_splited_input.last_part}
                {/arag_block}
            {/if}

            <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report_table">
            <tr>
                <td width="200" colspan="3">&nbsp;</td>
                <td colspan="3">
                    <input type="button" class="column_operator" value="+" />
                    <input type="button" class="column_operator" value="-" />
                    <input type="button" class="column_operator" value="*" />
                    <input type="button" class="column_operator" value="/" />
                    <input type="button" class="column_operator" value="%" />
                    <input type="button" class="column_operator" value="(" />
                    <input type="button" class="column_operator" value=")" />
                </td>
            </tr>
            <tr>
                <td width="30"><label for="column_label">_("Label"):</label></td>
                <td width="125"><input type="text" name="column_label" id="column_label" value="{$column_label|smarty:nodefaults|default:""}" /></td>
                <td width="45"><label for="formula">_("Formula"):</label></td>
                <td width="465"><input type="text" id="formula" name="formula" value="{$formula|smarty:nodefaults|default:""}" size="80" /></td>
                <td>
                    <label>&lt;&lt;&nbsp;
                    <select id="additional_column_columns">
                        <option>--_("Insert column")--</option>
                        {foreach from=$table item=column}
                            <option value="{$column.field}">{$column.field}</option>
                        {/foreach}
                    </select>
                    </label>
                </td>
                <td width="20"><input type="button" id="add_additional_column" value="+" /></td>
            </tr>
            {if !empty($additional_columns|smarty:nodefaults)}
                {foreach from=$additional_columns key=label item=column}
                    <tr>
                        <td>_("Label"):</td>
                        <td><div class="virtual_input">{$label}</div></td>
                        <td>_("Formula"):</td>
                        <td><div class="virtual_input">{$column}</div></td>
                        <td>&nbsp;</td>
                        <td>
                            <input type="hidden" name="columns_label[]" value="{$label}" />
                            <input type="hidden" name="formulas[]" value="{$column}" />
                            <input type="button" class="remove_additional_column" value="-" />
                        </td>
                    </tr>
                {/foreach}
            {/if}
            </table>

        {/arag_block}

        {arag_block template="collapsible" title="Complex Filter" status=$filters_block_status}

            {if isset($filter_error|smarty:nodefaults)}
                {arag_block align="left" dir="ltr" template="info"}
                    {$filter_splited_input.first_part}
                    <span style='color:red;'><b>{$filter_splited_input.unknown_part|default:"&nbsp;"}</b></span>
                    {$filter_splited_input.last_part}
                {/arag_block}
                {arag_block align="left" dir="ltr" template="error"}
                    {$filter_error}
                {/arag_block}
            {/if}

            <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report_table">
            <tr>
                <td>&nbsp;</td>
                <td colspan="4">
                    <input type="button" class="filter_operator" style="width:40px;" value="AND" />
                    <input type="button" class="filter_operator" style="width:40px;" value="OR" />
                    <input type="button" class="filter_operator" value="(" />
                    <input type="button" class="filter_operator" value=")" />
                    <input type="button" class="filter_operator" value="<=" />
                    <input type="button" class="filter_operator" value=">=" />
                    <input type="button" class="filter_operator" value="<" />
                    <input type="button" class="filter_operator" value=">" />
                    <input type="button" class="filter_operator" value="!=" />
                    <input type="button" class="filter_operator" value="=" />
                </td>
            </tr>
            <tr>
                <td width="30"><label for="filter">_("Filter"):</label></td>
                <td width="465">
                    <input type="text" id="filter" name="filter" value="{$filter|smarty:nodefaults|default:""}" size="80" />
                    <input type="hidden" name="filter_combine" value="OR" />
                </td>
                <td colspan="2">
                    <label>&lt;&lt;&nbsp;
                    <select id="filter_columns">
                        <option>--_("Insert column")--</option>
                        {foreach from=$table item=column}
                            <option value="{$column.field}">{$column.field}</option>
                        {/foreach}
                    </select>
                    </label>
                </td>
                <td width="20"><input type="button" id="add_filter" value="+" /></td>
            </tr>
            {if !empty($filters|smarty:nodefaults)}
                {foreach name="filters" from=$filters key=filter item=combine}
                    <tr>
                        <td>_("Filter"):</td>
                        <td><div class="virtual_input">{$filter|smarty:nodefaults}</div></td>
                        {if !$smarty.foreach.filters.last}
                        <td width="40"><label for="filter_combine">_("Combine"):</label></td>
                        <td>
                            <select name="filters_combine[]">
                                <option value="OR" {if $combine|smarty:nodefaults|default:"" == "OR"}selected="selected"{/if}>_("OR")</option>
                                <option value="AND" {if $combine|smarty:nodefaults|default:"" == "AND"}selected="selected"{/if}>
                                    _("AND")
                                </option>
                            </select>
                        </td>
                        {else}
                        <td colspan="2"><input type="hidden" name="filters_combine[]" value="OR" /></td>
                        {/if}
                        <td width="20" align="center">
                            <input type="hidden" name="filters[]" value="{$filter|smarty:nodefaults}" />
                            <input type="button" class="remove_filter" value="-" />
                        </td>
                    </tr>
                {/foreach}
            {/if}
            </table>

        {/arag_block}

        {arag_block template="blank" align="right"}
            {foreach from=$actions key=index item=action}
                <input type="hidden" name="actions[{$index}][uri]" value="{$action.uri|smarty:nodefaults|default:""}" />
                <input type="hidden" name="actions[{$index}][class_name]" value="{$action.class_name|smarty:nodefaults|default:""}" />
                <input type="hidden" name="actions[{$index}][tooltip]" value="{$action.tooltip|smarty:nodefaults|default:""}" />
                {if isset($action.group_action|smarty:nodefaults)}
                <input type="hidden" name="actions[{$index}][group_action]" value="{$action.group_action}" />
                {/if}
            {/foreach}
            <input type="hidden" name="parameter_name" value="{$parameter_name}" />
            <input type="hidden" name="report_name" value="{$report_name}" />
            <input type="hidden" name="report_description" value="{$report_description}" />
            <input type="hidden" name="table_name" value="{$table_name}" />
            <input type="submit" style="width:70px;height:20px;" value={quote}_("Preview"){/quote} />
            <input type="button" style="width:50px;height:20px;" id="save" value={quote}_("Save"){/quote} />
        {/arag_block}

    {/arag_form}
{/arag_block}

{arag_block}
    {arag_plist name="report"}
{/arag_block}
