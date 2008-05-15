{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

<script type="text/javascript">
    var table_desc       = {$table_desc|smarty:nodefaults};
    var fields           = {$fields|smarty:nodefaults};
    var fields_operators = {$operators|smarty:nodefaults};
    var fields_combines  = {$combines|smarty:nodefaults};    
    var right_align      = '{right}';
</script>
{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="modpub/report_generator/filters.js"}

{arag_form uri=$uri id="form"}

    {arag_block template="collapsible" title="Filters"}
        <div id="filters"></div>
        <label id="add_filter" style="float:{right}">
            _("Add filter"):
            <select id="filter_fields">
                <option>&nbsp;</option>            
                {foreach from=$table item=column}
                    <option value="{$column.field}">{$column.field}</option>
                {/foreach}
            </select>
        </label>
    {/arag_block}

    {arag_block template="blank" align="right"}
        <input type="submit" value={quote}_("Update"){/quote} />
    {/arag_block}

{/arag_form}

{arag_block}
    {arag_plist name="report"}
{/arag_block}
