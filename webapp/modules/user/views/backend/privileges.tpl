{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{if $parentid neq null}
{if $parentid eq 0}
    {assign var=uri value="user/backend/applications/privileges_parents"}
{else}
    {assign var=uri value="user/backend/applications/privileges/$parentid"}
{/if}
{arag_validation_errors}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("New label added!")
    {/arag_block}
{/if}
{arag_block}
    {arag_form uri=$uri}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("New Label"):{asterisk}
                </td>
                <td align="{left}">
                    <input type="text" name="newlabel" value="{$newlabel|smarty:nodefaults|default:null}" dir="ltr" />
                    <input type="hidden" value="{$parentid}" name="parentid" />
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
{/if}
{arag_block}
    {arag_plist name="privileges"}
{/arag_block}
