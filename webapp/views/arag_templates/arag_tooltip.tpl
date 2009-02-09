{arag_load_script src="scripts/mootools.js"}

{if $type == 'block'}
    <{if $inline}span{else}div{/if}{if isset($class|smarty:nodefaults)} class="{$class}"{/if}{if isset($style|smarty:nodefaults)} style="{$style}"{/if} id = "{$tip_id}">
        {$content|smarty:nodefaults}
    </{if $inline}span{else}div{/if}>
    <script type="text/javascript">
        //<![CDATA[
        var tip        = '{$tip_id}';
        var tip_title  = '{$tip_title|smarty:nodefaults}';
        var tip_text   = '{$tip_text|smarty:nodefaults}';
        var class_name = '{$tip_class}';
        {literal}
            $(tip).store('tip:title', tip_title);
            $(tip).store('tip:text', tip_text);
            var myTip = new Tips($(tip), {
                className: class_name
            });
        {/literal}
        //]]>
    </script>
{else}
    <a href="{$href}" title="{$tip_title|smarty:nodefaults}" id="{$tip_id}">
        {$content|smarty:nodefaults}
    </a>
    <script type="text/javascript">
        //<![CDATA[
        var tip        = '{$tip_id}';
        var tip_text   = '{$tip_text|smarty:nodefaults}';
        var class_name = '{$tip_class}';
        {literal}
            $(tip).store('tip:text', tip_text);
            var myTip = new Tips($(tip), {
                className: class_name
            });
        {/literal}
        //]]>
    </script>
{/if}
