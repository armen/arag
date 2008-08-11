{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

<div class="tabbed_block_title" align="{left}" dir="{dir}"
     style="background: transparent url({$arag_base_url|smarty:nodefaults}modpub/{$tabbedblock_module}/icon_48x48.png) {left} no-repeat;">
    {$tabbedblock_title}::{$tabbedblock_selected_tab_name}
</div>
<div style="clear:both;font-size:0px;height:0px;">&nbsp;</div>
<div class="tabbed_block_nav">
    <ul>
        {foreach from=$tabbedblock_items item=tabbedblock_item}
            {if $tabbedblock_item.is_parent}
                {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
                    <li class="selected" style="float:{left};">
                {else}
                    <li class="deselected" style="float:{left};">
                {/if}
                {if $tabbedblock_item.enabled}
                    {if $tabbedblock_item.is_url}
                        <a href="{$tabbedblock_item.uri}" target="_blank"
                           onclick="window.open(this.href); return false;"
                           onkeypress="window.open(this.href); return false;"
                           title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                    {else}
                        <a href="{$tabbedblock->genURL($tabbedblock_item.uri)}"
                           title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                    {/if}
                {/if}
                </li>
            {/if}
        {/foreach}
    </ul>
</div>
<div style="clear:both;font-size:0px;height:0px;">&nbsp;</div>
<div class="tabbed_block_content">
    {if $tabbedblock_selected_tab.has_selected_subtab}
    <div class="tabbed_block_subnav">
        <ul>
            {foreach from=$tabbedblock_items item=tabbedblock_item}
                {if isset($tabbedblock_item.parent_uri|smarty:nodefaults) &&
                    $tabbedblock_item.parent_uri == $tabbedblock_selected_tab.uri}

                    {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
                        <li class="selected" style="float:{left};">
                    {else}
                        <li class="deselected" style="float:{left};">
                    {/if}
                    {if $tabbedblock_item.enabled}
                        {if $tabbedblock_item.is_url}
                            <a href="{$tabbedblock_item.uri}" target="_blank"
                               onclick="window.open(this.href); return false;"
                               onkeypress="window.open(this.href); return false;"
                               title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                        {else}
                            <a href="{$tabbedblock->genURL($tabbedblock_item.uri)}"
                               title="{$tabbedblock_item.title|default:""}">{$tabbedblock_item.name}</a>
                        {/if}
                    {/if}
                    </li>
                {/if}
            {/foreach}
        </ul>
    </div>
    <div style="clear:both;font-size:0px;height:0px;">&nbsp;</div>
    {/if}

    {$tabbedblock_content|smarty:nodefaults}
</div>
