{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{assign var=types value=$component->getTypes()}
{assign var=refId value=$component->getReferenceId()}
{if !isset($refId|smarty:nodefaults)}
    {arag_form uri=$component->getUri() method="post"}
        <div>
            {arag_location name="location"}
        </div>
        <div style="float:{left};">
            <div id="attachment" style="display:none;">
                <input type="text" name="contacts[]" />
                {if is_array($types|smarty:nodefaults)}
                    <select name="types[]" />
                        {foreach from=$types item=type key=key}
                            {if $key != 'location'}
                                <option value="{$key}">{$type}</option>
                            {/if}
                        {/foreach}
                    </select>
                {else}
                    <input type="hidden" name="types[]" value="{$types}" />
                {/if}
            </div>
            <div id="attachments_container">
                <input type="text" name="contacts[]" />
                {if is_array($types|smarty:nodefaults)}
                    <select name="types[]" />
                        {foreach from=$types item=type key=key}
                            {if $key != 'location'}
                                <option value="{$key}">{$type}</option>
                            {/if}
                        {/foreach}
                    </select>
                {else}
                    <input type="hidden" name="types[]" value="{$types}" />
                {/if}
            </div>
        </div>
        <div style="float:{left};">
            <input type="hidden" name="key" value="{$component->getKey()}" />
            <input type="button" value="+" onclick="$('attachment').clone().injectAfter('attachments_container').set('style', 'display:block;');" />
        </div>
        <div style="clear:both;"></div>
    {/arag_form}
{else}
    {assign var=contacts value=$component->getContacts()}
    {if count($contacts)}
        <table border="0" width="100%" cellpadding="0" cellspacing="0" dir="{dir}">
            {if $component->onlyShow()}
                {foreach from=$contacts item=contact}
                    <tr>
                        <td width="150" align="{left}">
                            {assign var=type value=$contact->type}
                            {$types.$type}:
                        </td>
                        <td align="{left}">
                            {$contact->value}
                        </td>
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan="2">
                        <div id="attachment" style="display:none;">
                            <input type="text" name="contacts[]" />
                            {if is_array($types|smarty:nodefaults)}
                                <select name="types[]" />
                                    {foreach from=$types item=type key=key}
                                        {if $key != 'location'}
                                            <option value="{$key}">{$type}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            {else}
                                <input type="hidden" name="types[]" value="{$types}" />
                            {/if}
                        </div>
                        <div style="float:{left}">
                            {foreach from=$contacts item=contact}
                                {if $contact->type != 'location'}
                                    <div id="attachments_container">
                                        <input type="text" name="contacts[]" value="{$contact->value}" />
                                        <input type="hidden" name="ids[]" value="{$contact->id}" />
                                        {if is_array($types|smarty:nodefaults)}
                                            <select name="types[]" />
                                                {foreach from=$types item=type key=key}
                                                    {if $key != 'location'}
                                                        <option value="{$key}" {if $key == $contact->type}selected="selected"{/if}>{$type}</option>
                                                    {/if}
                                                {/foreach}
                                            </select>
                                        {else}
                                            <input type="hidden" name="types[]" value="{$types}" />
                                        {/if}
                                    </div>
                                {else}
                                    <div id="location_container">
                                        {arag_location name="location" value=$contact->value}
                                        <input type="hidden" name="location_id" value="{$contact->id}" />
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                        <div style="clear:both">
                            &nbsp;
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="key" value="{$component->getKey()}" />
                        <input type="button" value="+" onclick="$('attachment').clone().injectAfter('location_container').set('style', 'display:block;');" />
                    </td>
                </tr>
            {/if}
        </table>
    {/if}
{/if}
