<table dir="{dir}" class="location readonly">
    {foreach from=$path item='location'}
        {if isset($location.english|smarty:nodefaults)}
            <tr>
                <td>
                    {if $location.type == 'country'}
                        <img src="{$arag_base_url}/modpub/locations/images/{$location.type}/{$location.code|@strtolower}/flag.png" alt="{$location.english}" />
                    {else}
                        &nbsp;
                    {/if}
                </td>
                <td>
                    {$location.english}
                </td>
            </tr>
        {/if}
    {/foreach}
</table>