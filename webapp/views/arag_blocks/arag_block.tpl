{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{if isset($arag_block_title|smarty:nodefaults) && trim($arag_block_title) != ""}
    <div class="arag_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}
                           {$arag_block_id|smarty:nodefaults}>
        <div class="arag_block_title">{$arag_block_title}</div>
        <div class="arag_block_content arag_tags_ds">
            {$arag_block_content|smarty:nodefaults}
        </div>
    </div>
{else}
    <div class="arag_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}
                           {$arag_block_id|smarty:nodefaults}>
        <div class="arag_block_content arag_tags_ds">
            {$arag_block_content|smarty:nodefaults}
        </div>
    </div>
{/if}
