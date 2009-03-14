{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="scripts/MoodalBox/js/moodalbox.js"}

<a href="{$href}" rel="moodalbox{if isset($width|smarty:nodefaults)} {$width}{/if}{if isset($height|smarty:nodefaults)} {$height}{/if}"
   {if isset($class|smarty:nodefaults)}title="{$title}"{/if} {if isset($class|smarty:nodefaults)}class="{$class}"{/if}
   {if isset($html_id|smarty:nodefaults)}id="{$html_id}"{/if}>{$text}</a>
