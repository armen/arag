{arag_block template="warning"}
<table border="0" dir="{dir}">
<tr>
    <td>
        {capture assign="msg"}_("Do you really want to delete the '%s' Entry?"){/capture}
        {$msg|sprintf:$location.name}
    </td>
    <td>
        {arag_form uri=locations/backend/delete/`$location.id` method="post"}
            <input type="submit" value={quote}_("Yes"){/quote} />
        {/arag_form}
    </td>
    <td>
        {arag_form uri="locations/backend" method="post"}
            <input type="submit" value={quote}_("No"){/quote} />
        {/arag_form}
    </td>
</tr>
</table>
{/arag_block}