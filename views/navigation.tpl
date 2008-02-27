{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
{dir assign=dir}
{if $dir == 'rtl'}
    {assign var=separator value=$separator_rtl|smarty:nodefaults}
{else}
    {assign var=separator value=$separator_ltr|smarty:nodefaults}
{/if}

{arag_block template="blank"}
    <ul style="position:relative;padding:0px;margin:0px;">
    {foreach name=history from=$current_history.uri_map key=index item=uri}
        {if $smarty.foreach.history.last}
            <li style="float:right;">{$current_history.titles.$index}</li>
        {else}
            <li style="float:right;">{html_anchor uri=$uri title=`$current_history.titles.$index`}{$separator|smarty:nodefaults}</li>
        {/if}
    {/foreach}
    </ul>
{/arag_block}
<div style="clear:both;">&nbsp;</div>
