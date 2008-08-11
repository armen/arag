{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

<div class="arag_default_block"{$arag_block_direction|smarty:nodefaults}>
    <div class="arag_default_block_tr">
        <div class="arag_default_block_tl"{$arag_block_direction|smarty:nodefaults}>{$arag_block_title|default:"&nbsp;"}</div>
    </div>
    <div class="arag_default_block_left"><div class="arag_default_block_right">
        <div class="arag_default_block_body"{$arag_block_direction|smarty:nodefaults}>
            {$arag_block_content|smarty:nodefaults|default:"&nbsp;"}
        </div>
    </div></div>
    <div class="arag_default_block_br"><div class="arag_default_block_bl">&nbsp;</div></div>
</div>
