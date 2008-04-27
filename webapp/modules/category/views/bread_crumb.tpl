{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id: horizontal.tpl 432 2007-12-08 07:56:37Z sasan $
*}
{arag_block}
    {arag_block template="blank"}
        {foreach from=$breadCrumb item=bread key=key}
                {assign var=uri value=$category->parseURI($baseURI, $key)}
                {if $key!= 0}
                    >
                {/if}
                &nbsp;<a href="{helper function="url::site" uri=$uri}" title="{$bread}">{$bread}</a>
        {/foreach}
    {/arag_block}
{/arag_block}
