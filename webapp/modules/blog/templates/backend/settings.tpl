{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_block}
    
    {arag_validation_errors}

    {if $saved == 'saved'}
        {arag_block align="left" template="info"}
            _("Settings are updated successfuly!")
        {/arag_block}
    {/if}

    {arag_form uri="blog/backend/settings" method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td align="{right}" width="100">_("Limit"):</td>
        <td><input type="text" name="limit" value="{$limit|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value={quote}_("Save"){/quote} />
            <input type="reset" value={quote}_("Reset"){/quote} />            
        </td>
    </tr>
    </table>
    {/arag_form}

{/arag_block}
