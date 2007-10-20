{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z armen $
*}
{arag_block}
    
    {arag_validation_errors}

    {arag_block template="info"}
        {capture assign="info_msg"}_("Fields marked with a %s are required."){/capture}
        {asterisk message=$info_msg}
    {/arag_block}

    {arag_form uri="blog/backend/entry/edit" method="post"}
    {include file="backend/entry_form.tpl"}
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="hidden" name="id" value="{$id}" />
            <input type="submit" value={quote}_("Edit"){/quote} />
            <input type="reset" value={quote}_("Reset"){/quote} />            
        </td>
    </tr>
    </table>
    {/arag_form}
{/arag_block}
