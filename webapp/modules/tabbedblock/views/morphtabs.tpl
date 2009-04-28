{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{foreach from=$tabbedblock_items item=tabbedblock_item}
    {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
        {assign var="selected_id" value=$tabbedblock->parseURI($tabbedblock_item.uri)|replace:'/':'.'}
    {/if}
{/foreach}

{if !isset($selected_id|smarty:nodefaults)}
    {assign var="selected_id" value='first'}
{/if}

<script type="text/javascript" src="{$arag_base_url|smarty:nodefaults}scripts/morphtabs1.4.js"></script>
<script type="text/javascript">
    //<![CDATA[
        {literal}
        window.addEvent('domready', function() {
        {/literal}
            var tabbedblock = new MorphTabs('tabbedblock', {literal}{{/literal}

                changeTransition: {literal}{{/literal}
                                  transition: '{$transition|smarty:nodefaults|default:'linear'}',
                                  duration: '{$duration|smarty:nodefaults|default:fast}'
                                  {literal}}{/literal},
                mouseOverClass:   '{$mouse_over_class|smarty:nodefaults|default:'over'}',
                activateOnLoad:   '{$activate_on_load|smarty:nodefaults|default:$selected_id}',
                useAjax:          {$use_ajax|smarty:nodefaults|default:'false'},
                ajaxUrl:          '{$ajax_url|smarty:nodefaults|default:$tabbedblock->genURL('tabbedblock/frontend/tabcontroller/index/')}',
                ajaxOptions:      {literal}{{/literal}method:'{$ajax_method|smarty:nodefaults|default:'get'}'{literal}, evalScripts: true}{/literal},
                //evalScripts:      true,
                ajaxLoadingText:  '{$ajax_loading_text|smarty:nodefaults|default:'<div class="loading"><img src="/scripts/MoodalBox/img/loading.gif" alt="Loading..." width="47" height="39" \/><\/div>'}',
                width:            '{$width|smarty:nodefaults|default:'auto'}',
                height:           '{$height|smarty:nodefaults|default:'auto'}',
                panelStartFx:     '{$panel_start_fx|smarty:nodefaults|default:"fade"}',
                panelEndFx:       '{$panel_end_fx|smarty:nodefaults|default:"appear"}'
        {literal}
            });
        });
        {/literal}
    //]]>
</script>

<div id="tabbedblock" dir="{dir}">
    <ul class="morphtabs_title">
    {foreach from=$tabbedblock_items item=tabbedblock_item}
        {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
            <li title="{$tabbedblock->parseURI($tabbedblock_item.uri)|replace:'/':'.'}" class="active" style="float:{left};dir:{dir};">{$tabbedblock_item.name}</li>
        {elseif $tabbedblock_item.enabled}
            <li title="{$tabbedblock->parseURI($tabbedblock_item.uri)|replace:'/':'.'}" style="float:{left};dir:{dir};">{$tabbedblock_item.name}</li>
        {/if}
    {/foreach}
    </ul>

    {foreach from=$tabbedblock_items item=tabbedblock_item}
        {if isset($tabbedblock_item.selected|smarty:nodefaults) && $tabbedblock_item.selected}
            <div id="{$tabbedblock->parseURI($tabbedblock_item.uri)|replace:'/':'.'}" class="morphtabs_panel active">{if $use_ajax}{$tabbedblock_content|smarty:nodefaults}{else}&nbsp;{/if}</div>
        {elseif $tabbedblock_item.enabled}
            <div id="{$tabbedblock->parseURI($tabbedblock_item.uri)|replace:'/':'.'}" class="morphtabs_panel">&nbsp;</div>
        {/if}
    {/foreach}
</div>
