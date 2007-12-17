{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{arag_block}
    {arag_block template="blank"}    
        {arag_validation_errors}

        {arag_block template="info"}
            {capture assign="info_msg"}_("Fields marked with a %s are required."){/capture}
            {asterisk message=$info_msg}
        {/arag_block}

        {arag_form uri="comment/frontend/post" method="post"}
        <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{right}" width="100">{asterisk}_("Name"):</td>
            <td><input type="text" name="name" value="{$name|smarty:nodefaults|default:null}" /></td>
        </tr>
        <tr>
            <td align="{right}" width="100">_("Email"):</td>
            <td><input type="text" name="email" value="{$email|smarty:nodefaults|default:null}" /></td>
        </tr>
        <tr>
            <td align="{right}" width="100">_("Homepage"):</td>
            <td><input type="text" name="homepage" value="{$homepage|smarty:nodefaults|default:null}" /></td>
        </tr>
        <tr>
            <td align="{right}" width="100">{asterisk}_("Comment"):</td>
            <td><textarea name="comment"></textarea></td>
        </tr>        
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="submit" value={quote}_("Submit"){/quote} />
                <input type="reset" value={quote}_("Reset"){/quote} />            
            </td>
        </tr>
        </table>
        {/arag_form}
    {/arag_block}
{/arag_block}
