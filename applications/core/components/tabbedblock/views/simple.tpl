{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

<div style="clear:both;font-size:0px;height:0px;">&nbsp;</div>
<div class="tabbed_block_view">
    <div class="tabbed_block_simple">
        <ul class="tabbed_block_simple">
            {foreach from=$tabbedblock_items item=tabbedblock_item key=number}
                {if $number!=0}
                    <li>|</li>
                {/if}
                <li>
                {if $tabbedblock_item.enabled}
                    <a href="{$tabbedblock_item.href|default:""}" 
                    title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                {else}
                    {$tabbedblock_item.name}
                {/if}
                </li>        
            {/foreach}    
        </ul>
    </div>
</div>
<div>
    {$tabbedblock_content|smarty:nodefaults}
</div>
