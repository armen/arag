{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Label edited!")
    {/arag_block}
{/if}
{arag_block}
    {arag_form uri="user/backend/applications/privileges_edit/$id"}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Label"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="label" value="{$label|smarty:nodefaults|default:null}" dir="ltr"/>
                    <input type="hidden" value="{$id}" name="id" />
                </td>
            </tr>
            {if $parentid neq 0}
                <tr>
                    <td align="{right}">
                        _("Privilege"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="privilege" value="{$privilege|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
            {else}
                <input type="hidden" name="parentid" value="{$parentid}" />
            {/if}
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table> 
    {/arag_form}
{/arag_block}
