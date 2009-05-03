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
<div class="bread_crump_container">
    {foreach from=$config item=item key=key}
        {if $current_uri == $item.uri}
            <div class="bread_crump_items bread_crump_selected" style="float:{left}">
                <div class="breadcrumb{if isset($item.class|smarty:nodefaults)} {$item.class}_active{/if}">
                    {$item.title}
                </div>
            </div>
        {elseif in_array($item.uri, $visited_uris)}
            <div class="bread_crump_items bread_crump_not_selected" style="float:{left}">
                <div class="breadcrumb{if isset($item.class|smarty:nodefaults)} {$item.class}_deactive{/if}">
                    <a href="{kohana_helper function="url::site" uri=`$item.uri`}">
                        {$item.title}
                    </a>
                </div>
            </div>
        {elseif $show_next_steps}
            <div class="bread_crump_items bread_crump_not_visited" style="float:{left}">
                <div class="breadcrumb{if isset($item.class|smarty:nodefaults)} {$item.class}_not_visited{/if}">
                    {$item.title}
                </div>
            </div>
        {/if}
    {/foreach}
    <div style="clear:both;">&nbsp;</div>
</div>
