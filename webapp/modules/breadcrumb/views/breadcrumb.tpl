{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_header}
    <link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}modpub/breadcrumb/styles.css" />
    {if isset($css|smarty:nodefaults)}
        <link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}{$css}" />
    {/if}
{/arag_header}
<div class="breadcrumb_container">
    {foreach from=$config item=item key=key}
        {if $current_uri == $item.uri}
            <div class="breadcrumb_items breadcrumb_selected" style="float:{left}">
                <div class="breadcrumb{if isset($item.class|smarty:nodefaults)} {$item.class}_active{/if}">
                    {$item.title}
                </div>
                <div>
                {if $key == 0}
                    <table width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
                        <tr>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_current_{left}{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_current_{left}{/if}">
                            </td>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_current{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_current{/if}">
                            </td>
                            {if $number_of_visited_uris == 0}
                                <td class="breadcrumb_progress_bar breadcrumb_progress_bar_current_{right}{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_current_{right}{/if}">
                                </td>
                            {/if}
                        </tr>
                    </table>
                {elseif $key == $number_of_visited_uris}
                    <table width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
                        <tr>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_current{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_current{/if}">
                            </td>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_current_{right}{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_current_{right}{/if}">
                            </td>
                        </tr>
                    </table>
                {else}
                    <table width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
                        <tr>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_current{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_current{/if}">
                            </td>
                        </tr>
                    </table>
                {/if}
                </div>
            </div>
        {elseif in_array($item.uri, $visited_uris)}
            <div class="breadcrumb_items breadcrumb_not_selected" style="float:{left}">
                <div class="breadcrumb{if isset($item.class|smarty:nodefaults)} {$item.class}_deactive{/if}">
                    {capture assign=uri}{if isset($item.link|smarty:nodefaults)}{$item.link}{else}{$item.uri}{/if}{/capture}
                    <a href="{kohana_helper function="url::site" uri=`$uri`}">
                        {$item.title}
                    </a>
                </div>
                {if $key == 0}
                    <table width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
                        <tr>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_visited_{left}{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_visited_{left}{/if}">
                            </td>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_visited{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_visited{/if}">
                            </td>
                        </tr>
                    </table>
                {elseif $key == $number_of_visited_uris}
                    <table width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
                        <tr>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_visited{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_visited{/if}">
                            </td>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_visited_{right}{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_visited_{right}{/if}">
                            </td>
                        </tr>
                    </table>
                {else}
                    <table width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
                        <tr>
                            <td class="breadcrumb_progress_bar breadcrumb_progress_bar_visited{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_visited{/if}">
                            </td>
                        </tr>
                    </table>
                {/if}
            </div>
        {elseif $show_next_steps}
            <div class="breadcrumb_items breadcrumb_not_visited" style="float:{left}">
                <div class="breadcrumb{if isset($item.class|smarty:nodefaults)} {$item.class}_not_visited{/if}">
                    {$item.title}
                </div>
                <div class="breadcrumb_progress_bar breadcrumb_progress_bar_next_step{if isset($item.class|smarty:nodefaults)} {$item.class}_progress_bar {$item.class}_progress_bar_next_step{/if}">
                </div>
            </div>
        {/if}
    {/foreach}
    <div style="clear:both;">&nbsp;</div>
</div>
