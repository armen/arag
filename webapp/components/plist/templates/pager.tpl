{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{assign var=uri value=$plist->getURI()}
{if $namespace}
    {assign var=namespace value="_`$namespace`"}
{/if}

{if $pager.numpages > 1}
    <div class="pager_wrapper">
        <table border="0" cellpadding="0" cellspacing="0" class="pager" dir="ltr">
            <tr>
            {* Creating << & < *}
            {if $pager.firstpage != $pager.current}
                <td class="pager_ltlt">
                    {anchor uri=$plist->parseURI($uri, "page=page`$namespace`:`$pager.firstpage`;") title='&nbsp;&lt;&lt;&nbsp;'}
                </td>
                <td class="pager_lt">
                    {anchor uri=$plist->parseURI($uri, "page=page`$namespace`:`$pager.prevpage`;") title='&nbsp;&lt;&nbsp;'}
                </td>
            {else}
                <td class="pager_disabled_ltlt">&nbsp;&lt;&lt;&nbsp;</td><td class="pager_disabled_lt">&nbsp;&lt;&nbsp;</td>
            {/if}
            {* end of Creating << & < *}
            {* Creating ... *}
            {if $pager.firstpage != reset(array_keys($pager.pages))}
                <td class="pager_dots">...</td>
            {/if}
            {* end of creating ... *}
            {* Creating page numbers *}
            {foreach from=$pager.pages key=pagenum item=from}
                <td class="pager_number">
                {if $pager.current != $pagenum}
                    {* Generating URL for pages *}
                    &nbsp;{anchor uri=$plist->parseURI($uri, "page=page`$namespace`:`$pagenum`;") title=$pagenum}&nbsp;
                {else}
                    &nbsp;{$pagenum}&nbsp;
                {/if}
                </td>
            {/foreach}
            {* end of Creating page numbers *}
            {* Creating ... *}
            {if $pager.lastpage != end(array_keys($pager.pages))}
                <td class="pager_dots">...</td>
            {/if}
            {* end of creating ... *}
            {* Creating >> & > *}
            {if $pager.lastpage != $pager.current}
                {* Generating URL for >> *}
                <td class="pager_gt">
                    {anchor uri=$plist->parseURI($uri, "page=page`$namespace`:`$pager.nextpage`;") title='&nbsp;&gt;&nbsp;'}
                </td>
                <td class="pager_gtgt">
                    {anchor uri=$plist->parseURI($uri, "page=page`$namespace`:`$pager.lastpage`;") title='&nbsp;&gt;&gt;&nbsp;'}
                </td>
            {else}
                <td class="pager_disabled_gt">&nbsp;&gt;&nbsp;</td><td class="pager_disabled_gtgt">&nbsp;&gt;&gt;&nbsp;</td>        
            {/if}
            {* end of Creating >> & > *}
            </tr>
        </table>
    </div>
{/if}
