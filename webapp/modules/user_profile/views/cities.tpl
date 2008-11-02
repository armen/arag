{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{literal}{{/literal}"cities":[
{foreach from=$cities item=city key=key}
	{literal}{{/literal}"city":"{$city.city}", "code":"{$city.code}"{literal}}{/literal}
    {assign var="last" value=$key+1}
    {if isset($cities.$last|smarty:nodefaults)}
        ,
    {/if}
{/foreach}
]{literal}}{/literal}
