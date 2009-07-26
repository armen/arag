<table dir="{dir}" class="location readonly">
    {foreach from=$path item='location'}
        {if isset($location.type|smarty:nodefaults)}
            <tr>
                <td>
                    {if $location.type == 'country'}
                        <img src="{$arag_base_url}/modpub/locations/images/{$location.type}/{$location.code|@strtolower}/flag.png" alt="{$location.english}" />
                    {else}
                        &nbsp;
                    {/if}
                </td>
                <td>
                    {if $location.name|@strlen > 1}
                        {$location.name}
                    {elseif $location.english|@strlen > 1}
                        {$location.english}
                    {else}
                        {$location.code}
                    {/if}
                </td>
            </tr>
        {/if}
    {/foreach}
</table>