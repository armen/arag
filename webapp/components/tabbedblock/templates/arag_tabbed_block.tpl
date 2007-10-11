{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

<div class="tabbed_block_title" align="{left}" dir="{dir}" 
     style="background: transparent url({$tabbedblock_module_icon_url}) {left} no-repeat;">
    {$tabbedblock_title}::{$tabbedblock_selected_tab_name}
</div>
<div style="clear:both;font-size:0px;height:0px;">&nbsp;</div>
<div class="tabbed_block_view">
    <div class="tabbed_block_nav">
        <ul>
            {foreach from=$tabbedblock_items item=tabbedblock_item}
                {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
                    <li class="selected" style="float:{left};">
                {else}
                    <li class="deselected" style="float:{left};">
                {/if}
                {if $tabbedblock_item.enabled}
                    <a href="{$tabbedblock->genURL($tabbedblock_item.uri)}" 
                       title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                {else}
                    <div title="{$tabbedblock_item.title}">{$tabbedblock_item.name}</div>
                {/if}        
                </li>
            {/foreach}
        </ul>
    </div>
</div>
<div class="tabbed_block_content">    
    {if isset($tabbedblock_selected_tab.has_subtab|smarty:nodefaults)}
    <div class="tabbed_block_subnav">
        <ul>
            {foreach from=$tabbedblock_selected_tab.subtabs item=tabbedblock_item}
                {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
                    <li class="selected" style="float:{left};">
                {else}
                    <li class="deselected" style="float:{left};">
                {/if}
                {if $tabbedblock_item.enabled}
                    <a href="{$tabbedblock->genURL($tabbedblock_item.uri)}" 
                       title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                {else}
                    <div title="{$tabbedblock_item.title}">{$tabbedblock_item.name}</div>
                {/if}        
                </li>
            {/foreach}
        </ul>
    </div>
    <div style="clear:both;font-size:0px;height:0px;">&nbsp;</div>    
    {/if}    

    {$tabbedblock_content|smarty:nodefaults}
</div>
