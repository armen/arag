{arag_load_script src="modpub/theme_manager/js/colorpicker/color_functions.js"}
{arag_load_script src="modpub/theme_manager/js/colorpicker/js_color_picker_v2.js"}
<div id="color{$ending}_container">
    <input type="text" name="{if $name}{$name}{else}color{$ending}{/if}" id="color{$ending}" value="{$color|smarty:nodefaults|default:null}" size="7" dir="ltr" style="background-color: {$color|smarty:nodefaults|default:null};" />
    <img src="{$arag_base_url|smarty:nodefaults}modpub/theme_manager/images/kcoloredit.gif" border="0" onclick="openColorPicker(this,'color{$ending}','{$arag_base_url|smarty:nodefaults}modpub/theme_manager/colorpicker.php')" />
</div>

