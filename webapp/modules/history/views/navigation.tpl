{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{arag_block template="blank"}
    <ul style="position:relative;padding:0px;margin:0px;" dir="{dir}">
    {foreach name=history from=$current_history.uri_map key=index item=uri}
        {if $smarty.foreach.history.last}
            <li style="float:{left};">{$current_history.titles.$index}</li>
        {else}
            <li style="float:{left};">{kohana_helper function="html::anchor" uri=$uri title=`$current_history.titles.$index`}{$separator|smarty:nodefaults}</li>
        {/if}
    {/foreach}
    </ul>
{/arag_block}
<div style="clear:both;">&nbsp;</div>
