{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

<div class="arag_warning_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}>
    <div class="arag_warning_block_content arag_tags_ds"
         style="background: #fff7db url({$arag_base_url}/images/messages/warning.png) no-repeat {$arag_icon_align} top;">
        {$arag_block_content|smarty:nodefaults}
    </div> 
</div>
