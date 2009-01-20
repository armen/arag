{*Smarty*}
{arag_block}
    {if isset($flagsaved|smarty:nodefaults) && $flagsaved}
        {arag_block align="left" template="info"}
            _("Province saved successfuly!")
        {/arag_block}
    {/if}
    {arag_plist name="provinces"}
{/arag_block}
