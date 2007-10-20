{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: post.tpl 60 2007-10-14 03:52:51Z armen $
*}
{arag_block}
    
    {arag_validation_errors}

    {arag_block template="info"}
        {capture assign="info_msg"}_("Fields marked with a %s are required."){/capture}
        {asterisk message=$info_msg}
    {/arag_block}

    {arag_form uri="blog/backend/entry/post" method="post"}
    {include file="backend/entry_form.tpl"}
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value={quote}_("Create"){/quote} />
            <input type="reset" value={quote}_("Reset"){/quote} />            
        </td>
    </tr>
    </table>
    {/arag_form}
{/arag_block}
