{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

<div class="arag_error_block"{$arag_block_direction|smarty:nodefaults}{$arag_block_align|smarty:nodefaults}>
    <div class="arag_error_block_content arag_tags_ds"
         style="background: #ffd9d9 url({$arag_base_url}/images/messages/error.png) no-repeat {$arag_icon_align} top;">
        {$arag_block_content|smarty:nodefaults}
    </div> 
</div>
