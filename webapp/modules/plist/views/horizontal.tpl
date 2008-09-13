{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/plist.js"}

{assign var=columns value=$plist->getColumns()}
{assign var=columnNames value=$plist->getColumnNames()}
{assign var=actions value=$plist->getActions()}
{assign var=group_actions value=$plist->getGroupActions()}
{assign var=virtualColumns value=$plist->getVirtualColumns()}
{assign var=pager value=$plist->getPager()}
{assign var=sums value=$plist->getSums()}

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
                            {if !$columns.$name.virtual && in_array($name, $sums)}
                                {assign var=_$name value=0}
                            {/if}
                        {/if}
                    {/foreach}

                    {if count($actions) > 0}
                        <th colspan="{$plist->getActionsCount()}">_("Actions")</th>
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
                                {if !$columns.$name.virtual && in_array($name, $sums)}
                                    {assign var="temp" value=$row.$name|default:0}
                                    {arag_get_var assign="item" var=_$name}
                                    {assign var=_$name value="`$item+$temp`"}
                                {/if}
                                <td {$onclick|smarty:nodefaults}>{$row.$name|default:"&nbsp;"}</td>
                            {elseif isset($columns.$name|smarty:nodefaults) && $columns.$name.virtual}
                                <td {$onclick|smarty:nodefaults}>{$plist->callCallback($name, $row)|smarty:nodefaults}</td>
                            {/if}
                        {/foreach}
                    {else}
                        <td>{$row}</td>
                    {/if}

                    {if count($actions) > 0}
                        {foreach from=$actions item=action}
                            <td class="plist_icon">
                                {if $action.callback|smarty:nodefaults}
                                    {assign var="action" value=$plist->callCallback($action.callback,$row)}
                                {/if}
                                {if $action.uri}
                                    {assign var=uri value=$plist->parseURI($action.uri, $row)}
                                    <a href="{kohana_helper function="url::site" uri=$uri}" title="{$action.label}" class="{$action.className}"
                                        target="{$action.target}">{$action.label}</a>
                                {else}
                                    <div title="{$action.label}" class="{$action.className}">{$action.label}</div>
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
                            {if !$columns.$name.virtual && in_array($name, $sums)}
                                <td>{arag_get_var var=_$name}</td>
                            {else}
                                <td>-</td>
                            {/if}
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
                            <option value="{kohana_helper function="url::site" uri=$action.uri}" title="{$action.label}">{$action.label}</option>
                        {/foreach}
                        </select>

                    {else}

                        <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                        {foreach from=$group_actions item=action}
                            <td class="plist_icon">
                                <a href="{kohana_helper function="url::site" uri=$action.uri}" title="{$action.label}" class="{$action.className}"
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
