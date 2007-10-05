{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{assign var=columns value=$plist->getColumns()}
{assign var=columnNames value=$plist->getColumnNames()}
{assign var=actions value=$plist->getActions()}
{assign var=group_actions value=$plist->getGroupActions()}
{assign var=virtualColumns value=$plist->getVirtualColumns()}
{assign var=pager value=$plist->getPager()}

<table border="0" cellpadding="0" cellspacing="0" dir="{dir}" width="100%" class="plist" >
    <caption dir="{dir}">&nbsp;</caption>
    {if $plist->hasHeader() && count($columns) > 0}
    <tr>
        {if count($group_actions)}
            <th>-</th>
        {/if}

        {foreach from=$columnNames item=name}
            {if isset($columns.$name|smarty:nodefaults) && !$columns.$name.hidden}
                <th>{$columns.$name.label}</th>
            {/if}
        {/foreach}

        {if count($actions) > 0}
            <th colspan="{$plist->getActionsCount()}">Actions</th>
        {/if}
    </tr>
    {/if}

    {foreach name=list from=$plist item=row key=key}
        <tr class="{cycle values="plist_odd,plist_even"}">
        {if is_array($row) && count($row) > 0}
            {foreach from=$row|smarty:nodefaults item=field key=name}
                {if count($columns) == 0 || (isset($columns.$name|smarty:nodefaults) && !$columns.$name.hidden)}
                    <td>{$field}</td>
                {/if}
            {/foreach}
        {else}
            <td>{$row}</td>
        {/if}

        {if count($virtualColumns) > 0}
            {foreach from=$virtualColumns item=column key=callback}
                <td>{$plist->callCallback($callback, $row)}</td>
            {/foreach}
        {/if}

        {if count($actions) > 0}
            {foreach from=$actions item=action}
                {assign var=uri value=$plist->parseURI($action.uri, $row)}
                <td>
                    {if isset($action.alternate_callback|smarty:nodefaults) && $action.alternate_callback != false}
                        {if $plist->callCallback($action.alternate_callback, $row)}
                            {anchor uri=$uri title=$action.label attributes="`$action.class_attribute`"}
                        {else}
                            {$action.label}
                        {/if}
                    {else}
                        {anchor uri=$uri title=$action.label attributes="`$action.class_attribute`"}
                    {/if}
                </td>
            {/foreach}
        {/if}
        </tr>
    {foreachelse}
        There is no record!
    {/foreach}

    {if $plist->hasFooter() && count($columns) > 0}    
    <tr class="plist_footer">
        {foreach from=$columnNames item=name}
            {if isset($columns.$name|smarty:nodefaults) && !$columns.$name.hidden}
                <td>-</td>
            {/if}
        {/foreach}

        {if count($actions) > 0}
            <td colspan="{$plist->getActionsCount()}">&nbsp;</td>
        {/if}
    </tr>
    {/if}
    
</table>

{include file="`$plist_templates_path`/pager.tpl"}
