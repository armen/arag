{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{assign var=pager value=$plist->getPager()}

{arag_block template="blank"}

    {if $plist->getResourceCount() > 0}
        <div class="blog">
            {foreach name=list from=$plist item=row key=key}
                <div class="blog_entry">
                    
                    <div class="blog_subject">{html_anchor uri=$entry_uri|replace:'#id#':$row.id title=$row.subject}</div>
                    <div class="blog_posted">
                        {capture assign="posted"}_("Posted by %s at %s"){/capture}
                        {$posted|sprintf:$row.author:$plist->callCallback('Blog.getDate', $row)}
                    </div>
                    <div class="blog_body">{$row.entry|smarty:nodefaults}</div>
                    {if isset($extended|smarty:nodefaults) && $extended}
                    <div class="blog_extended_body">{$row.extended_entry|smarty:nodefaults}</div>
                    {/if}
                </div>                    
            {/foreach}
        </div>

        {include file="`$plist_templates_path`/pager.tpl"}
    {/if}

{/arag_block}
