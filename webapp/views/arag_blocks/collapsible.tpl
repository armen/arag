{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/arag_collapsible_block.js"}

{if isset($arag_block_title|smarty:nodefaults) && trim($arag_block_title) != ""}
    <div class="arag_collapsible_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}>
        <fieldset class="{$status|smarty:nodefaults|default:"expanded"}">
            <legend>{$arag_block_title}</legend>
            <div>
                {$arag_block_content|smarty:nodefaults}
            <div>
        </fieldset>
    </div>
{else}
    <div class="arag_collapsible_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}>
        <fieldset class="{$status|smarty:nodefaults|default:"expanded"}">
            <legend>&nbsp;</legend>
            <div>
                {$arag_block_content|smarty:nodefaults}
            <div>
        </fieldset>
    </div>
{/if}
