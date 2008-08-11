{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: info.tpl 1 2007-10-05 06:54:57Z armen $
*}

<div class="arag_tips_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}>
    <div class="arag_tips_block_content arag_tags_ds"
         style="background: #ddffff url({$arag_base_url}/images/messages/tips.png) no-repeat {$arag_icon_align} top;">
        {$arag_block_content|smarty:nodefaults}
    </div>
</div>
