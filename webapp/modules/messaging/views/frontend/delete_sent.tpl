{*Smarty*}
{arag_tabbed_block name="global_tabs"}
    {arag_block }
        {arag_block template="warning" }
            <table border="0" dir="{dir}">
                <tr>
                    <td>
                        {capture assign="delete_msg"}
            	            _("Are you really want to delet the '%s' message?")
            	        {/capture}
            	        {$delete_msg|sprintf:$subject}
    	            </td>
    	            <td>
        	            {arag_form uri="messaging/frontend/inbox/delete_sent" method="post"}
        	                <input type="hidden" name="id" value="{$id}">
                            <input type="submit" value={quote}_("Yes"){/quote}>
        	            {/arag_form}
    	            </td>
    	            <td>
        	            {arag_form uri="messaging/frontend/inbox/sent_messages" method="post"}
        	               <input type="submit" value={quote}_("No"){/quote}>
        	            {/arag_form}
    	            </td>
                </tr>
            </table>
        {/arag_block}
    {/arag_block}
{/arag_tabbed_block}
