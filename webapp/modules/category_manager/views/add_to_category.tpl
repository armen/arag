<select name="{$name|smarty:nodefaults}[]" id="{$name|smarty:nodefaults}" multiple="multiple">
    {foreach from=$category_manager->getCategories($current_category.id, null, null, true, true) item=child}
        <option label="{$child.label|smarty:nodefaults}" value="{$child.id|smarty:nodefaults}" {if $selected|smarty:nodefaults|default:null && in_array($child.id, $selected)}selected="selected"{/if}>{section name=indent loop=$child.level}&nbsp;&nbsp;&nbsp;{/section}{$child.label|smarty:nodefaults}</option>
    {/foreach}
</select>
