{arag_block}
    <ul style="padding: 0px; margin: 0px;">
        {foreach from=$plugins item='plugin'}
            <li style="padding: 0px; margin: 0px;">
                <img src="{$arag_base_url}/{$plugin->image}" alt="" />
            </li>
        {/foreach}
    </ul>
{/arag_block}