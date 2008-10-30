{foreach from=$helps item='help'}
    {arag_block title=$help.title template=$help.type}
        {$help.message|smarty:nodefaults}
    {/arag_block}
{/foreach}