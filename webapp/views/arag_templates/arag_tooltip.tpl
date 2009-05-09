{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_header}
    <script type="text/javascript">
        //<![CDATA[
            {literal}
            window.addEvent('domready', function() {
            {/literal}
                var activator  = '{$activator}';
                var title      = "{$title|smarty:nodefaults}";
                var content    = "{$content|smarty:nodefaults}";
                var class_name = '{$class_name}';
                {literal}
                    $(activator).store('tip:title', title);
                    $(activator).store("tip:text", content);
                    var myTip = new Tips($(activator), {
                            className: class_name,
                            offsets: {'x': {/literal}{$x|smarty:nodefaults|default:16}, 'y':{$y|smarty:nodefaults|default:16}{literal}}
                        });

            });
            {/literal}
        //]]>
    </script>
{/arag_header}
