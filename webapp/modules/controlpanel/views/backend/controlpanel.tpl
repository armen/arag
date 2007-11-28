{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}
{arag_block}

    <div class="cp_items_box">
        <div class="cp_centerer">
            {foreach name=admin from=$modules item=module}
                {if ($smarty.foreach.admin.iteration-1) % 7 == 0}
                    <div class="cp_spacer">&nbsp;</div>
                {/if}
                <div class="cp_item">
                    <a href="{url_site uri="`$module.module`/backend"}">
                    <img src="{$arag_base_url|smarty:nodefaults}/images/modules/{$module.module}.png" 
                         width="48" height="48" alt="{$module.description}" />
                    </a>
                    <p class="cp_link">
                        <a href="{url_site uri="`$module.module`/backend"}">
                            {$module.name}
                        </a>    
                    </p>
                </div>
            {/foreach}
            <div class="cp_spacer">&nbsp;</div>
        </div>
    </div>
    
{/arag_block}
