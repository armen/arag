{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: decorator.tpl 41 2007-10-11 04:12:18Z sasan $
*}
{arag_block}
    {arag_validation_errors}
    {arag_form uri="multisite/backend/site/index"}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Application Name"):
                </td>
                <td align="{left}">
                    <input type="text" name="name" value="{$name|smarty:nodefaults}" />
                </td>
            </tr>
            <tr>
                 <td align="{right}">
                    _("Database ID"):
                </td>
                <td align="{left}">
                    <select name="dbid">
                        {if $dbid == ""}
                            <option value="" selected="selected">All</option>
                        {else}
                            <option value="">All</option>
                        {/if}
                        {foreach from=$ids item=id}
                            {if $id.database_id == $dbid}
                                <option value="{$id.database_id}" selected="selected">{$id.name}</option>
                            {else}
                                <option value="{$id.database_id}">{$id.name}</option>
                            {/if}
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Search"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
{arag_block}
    {arag_plist name="applications"}
{/arag_block}
