{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: horizontal.tpl 432 2007-12-08 07:56:37Z sasan $
*}
{assign var=categories value=$category->build()}
{assign var=baseURI value=$category->getURI()}
{assign var=columns value=$category->getColumns()}
{assign var=finalURI value=$category->getFinalURI()}
{assign var=breadCrumb value=$category->getBreadCrumb()}
{if $category->getSubCatCount() > 0}
    {include file="`$category_templates_path`bread_crumb.tpl"}
    {arag_block}
        {arag_block template="blank"}
            <table border="0" width="100%" dir="{dir}">
                {counter start=$columns skip=1 print=false assign=count}
                {foreach from=$categories item=cat}
                    {if ($count%$columns) == 0}
                        <tr>
                    {/if}
                        <td align="{left}">
                            {assign var=catcount value=$category->getSubCatCount($cat.module_name, $cat.id)}
                            {if $catcount > 0}
                                {assign var=uri value=$category->parseURI($baseURI, $cat.id)}
                                <a href="{url_site uri=$uri}" title="{$cat.name}">{$cat.name}</a>&nbsp;({$catcount})
                            {else}
                                {if $finalURI}
                                    {assign var=href value=$category->parseURI($finalURI, null, $cat.id)}
                                    <a href="{url_site uri=$href}">{$cat.name}</a>
                                {else}
                                    {$cat.name}
                                {/if}
                            {/if}
                            {counter}
                        </td>
                    {if ($count%$columns) == 0}
                        </tr>
                    {/if}
                {/foreach}
                </tr>
            </table>
        {/arag_block}
    {/arag_block}
{else}
    {arag_block template="blank"}
        <div class="category_norecords">
            {$category->getEmptyListMessage()}
        </div>
    {/arag_block}
{/if}
