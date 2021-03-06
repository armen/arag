{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Privileges edited successfully!")
    {/arag_block}
{/if}
{arag_block}
    {if $flagform}
        {assign var=uri value="user/backend/applications/group_privileges_edit/$id/$appname"}
    {else}
        {assign var=uri value="user/backend/application/group_privileges_edit/$id/$appname"}
    {/if}
    {arag_form uri=$uri}
        <table border="0" cellpadding="0" cellspacing="0" dir="{dir}" width="100%" class="prilist" >
            {foreach from=$parent_privileges item=object}
                <tr class="prilist_parent">
                    <td align="{left}">
                        <label>
                        {if $object.selected}
                            <input type="checkbox" name="privileges[]" value="{$object.id}" checked="checked" />&nbsp;<b dir="ltr">{$object.label}</b>
                        {else}
                            <input type="checkbox" name="privileges[]" value="{$object.id}" />&nbsp;<b dir="ltr">{$object.label}</b>
                        {/if}
                        </label>
                    </td>
                </tr>
                {foreach from=$sub_privileges[$object.id] item=item}
                    <tr class="prilist_sub">
                        <td align="{left}">
                            <label>
                            {if $item.selected}
                                <input type="checkbox" name="privileges[]" value="{$item.id}" checked="checked" />&nbsp;<span dir="ltr">{$item.label}&nbsp;({$item.privilege})</span>
                            {else}
                                <input type="checkbox" name="privileges[]" value="{$item.id}" />&nbsp;<span dir="ltr">{$item.label}&nbsp;({$item.privilege})</span>
                            {/if}
                            </label>
                        </td>
                    </tr>
                {/foreach}
            {/foreach}
            <tr class="prilist_parent">
                <td>
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
        <input type="hidden" name="id" value="{$id}" />
        <input type="hidden" name="appname" value="{$appname}" />
        {/arag_form}
{/arag_block}
