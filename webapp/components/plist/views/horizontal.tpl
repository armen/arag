{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{if !isset($_loaded|smarty:nodefaults)}
    {assign var=_loaded value=True}
    <script type="text/javascript" src="{$arag_base_url|smarty:nodefaults}scripts/mootools.js"></script>    
    <script type="text/javascript" src="{$arag_base_url|smarty:nodefaults}scripts/plist.js"></script>
{/if}

{assign var=columns value=$plist->getColumns()}
{assign var=columnNames value=$plist->getColumnNames()}
{assign var=actions value=$plist->getActions()}
{assign var=group_actions value=$plist->getGroupActions()}
{assign var=virtualColumns value=$plist->getVirtualColumns()}
{assign var=pager value=$plist->getPager()}

{arag_block template="blank"}

    {if $plist->getResourceCount() > 0}

        {arag_form method="post" id="plist_$namespace" class="plist_form"}
            <table border="0" cellpadding="0" cellspacing="0" dir="{dir}" width="100%" class="plist" >
                <caption dir="{dir}">&nbsp;</caption>
                {if $plist->hasHeader() && count($columns) > 0}
                <tr>
                    {if count($group_actions) > 0}
                        <th class="plist_group_actions_col"><input type="checkbox" onclick="toggleCheckboxesStatus(this.checked, '{$namespace}');" /></th>
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
                    
                    {capture assign="class"}{cycle values="plist_odd,plist_even"}{/capture}
                    <tr class="{$class}" onmouseover="listMouseEvent(this, 'over', '{$class}')" onmouseout="listMouseEvent(this, 'out', '{$class}')" 
                        onclick="listMouseEvent(this, 'click', '{$class}')">

                    {assign var="onclick" value=""}
                    {if count($group_actions) > 0}
                        {assign var="parameter_name" value=$plist->getGroupActionParameterName()}
                        <td>
                            {if isset($row.$parameter_name|smarty:nodefaults)}
                                {assign var="onclick" value="onclick=\"toggleListCheckbox($('`$namespace``$parameter_name`_`$row.$parameter_name`'))\""}
                                <input type="checkbox" name="{$parameter_name}[]" value="{$row.$parameter_name}" 
                                       id="{$namespace}{$parameter_name}_{$row.$parameter_name}" />
                            {/if}
                        </td>
                    {/if}

                    {if is_array($row) && count($row) > 0}
                        {foreach from=$columnNames item=name}
                            {if count($columns) == 0 || (isset($columns.$name|smarty:nodefaults) && !$columns.$name.hidden && !$columns.$name.virtual)}
                                <td {$onclick|smarty:nodefaults}>{$row.$name|default:"&nbsp;"}</td>
                            {elseif isset($columns.$name|smarty:nodefaults) && $columns.$name.virtual}
                                <td {$onclick|smarty:nodefaults}>{$plist->callCallback($name, $row)}</td>
                            {/if}
                        {/foreach}
                    {else}
                        <td>{$row}</td>
                    {/if}

                    {if count($actions) > 0}
                        {foreach from=$actions item=action}
                            <td class="plist_icon">
                                {if isset($action.alternate_callback|smarty:nodefaults) && 
                                    $action.alternate_callback != false && 
                                    $plist->callCallback($action.alternate_callback, $row)}

                                    {if $action.alternate_uri != null}
                                        {assign var=uri value=$plist->parseURI($action.alternate_uri, $row)}
                                        <a href="{url_site uri=$uri}" title="{$action.title}" class="{$action.class_name}_alt" 
                                           target="{$action.target}">{$action.label}</a>
                                    {else}
                                        <div title="{$action.title}" class="{$action.class_name}_alt">{$action.label}</div>
                                    {/if}
                                {else}
                                    {assign var=uri value=$plist->parseURI($action.uri, $row)}                                
                                    <a href="{url_site uri=$uri}" title="{$action.title}" class="{$action.class_name}" 
                                       target="{$action.target}">{$action.label}</a>
                                {/if}
                            </td>
                        {/foreach}
                    {/if}
                    </tr>
                {/foreach}

                {if $plist->hasFooter() && count($columns) > 0}    
                <tr class="plist_footer">

                    {if count($group_actions) > 0}
                        <td><input type="checkbox" onclick="toggleCheckboxesStatus(this.checked, '{$namespace}');" /></td>
                    {/if}
                
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

            {if count($group_actions) > 0}
                <div class="plist_group_actions plist_group_actions_{dir}">
                    {if $plist->getGroupActionType() == 'select'}

                        <select onchange="listForward(this, '{$namespace}')">
                        <option value="">- Select an action -</option>
                        {foreach from=$group_actions item=action}
                            <option value="{url_site uri=$action.uri}" title="{$action.title}">{$action.label}</option>
                        {/foreach}
                        </select>

                    {else}
                        
                        <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                        {foreach from=$group_actions item=action}
                            <td class="plist_icon">
                                <a href="{url_site uri=$action.uri}" title="{$action.title}" class="{$action.class_name}"
                                   onclick="listForward(this, '{$namespace}')">{$action.label}</a>
                            </td>
                        {/foreach}
                        </tr>
                        </table>
                        
                    {/if}
                </div>
            {/if}
        {/arag_form}

        {include file="`$plist_templates_path`pager.tpl"}

    {else}
        <div class="plist_norecords">
            {$plist->getEmptyListMessage()}
        </div>
    {/if}

{/arag_block}
