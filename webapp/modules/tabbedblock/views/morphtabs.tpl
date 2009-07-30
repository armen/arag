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
<script type="text/javascript">
{literal}
    Request.HTML.implement({
        success : function(text) {
            var html    = this.processHTML(this.response.text);
            var scripts = html.getElements('script');

            scripts.each(function(script) {
                if (script.get('src') == null) {
                    return ;
                }
                if ($$('script[src='+script.get('src')+']').length < 1) {
                    Asset.javascript(script.get('src'));
                }
            });

            var options = this.options, response = this.response;
        
            response.html = text.stripScripts(function(script){
                response.javascript = script;
            });
            
            var temp = this.processHTML(response.html);
            
            response.tree = temp.childNodes;
            response.elements = temp.getElements('*');
            
            if (options.filter) response.tree = response.elements.filter(options.filter);
            if (options.update) $(options.update).empty().adopt(response.tree);
            if (options.evalScripts) $exec(response.javascript);
            
            this.onSuccess(response.tree, response.elements, response.html, response.javascript);
        }
    });
{/literal}
</script>
{if !isset($selected_id|smarty:nodefaults)}
    {assign var="selected_id" value='first'}
{/if}
{arag_load_script src="scripts/mootools/core.js"}
{arag_load_script src="scripts/mootools/utilities.js"}
{arag_load_script src="scripts/morphtabs1.4.js"}
{arag_header}
    <script type="text/javascript">
        //<![CDATA[
            {literal}
            window.addEvent('load', function() {
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
                    evalScripts:      true,
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
{/arag_header}
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
