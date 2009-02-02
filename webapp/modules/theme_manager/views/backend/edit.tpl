{*Smarty*}
{arag_validation_errors}
{arag_block}
{if isset($flagsaved|smarty:nodefaults) && $flagsaved}
    {arag_block align="left" template="info"}
        _("Styles saved successfuly!")
    {/arag_block}
{/if}
{arag_form uri="theme_manager/backend/edit" method="post" enctype="multipart/form-data"}
    {arag_block}
         <table border="0" dir="{dir}">
             {foreach from=$styles item=style key=key}
             <tr>
                 <td align="{right}">{$style.description}:</td>
                 <td align="{left}">
                    {if $style.type=="color"}
                        {arag_colorpicker color=$style.value ending=$key name="$key"}
                    {else}
                        <input type="file" name="{$key}" />&nbsp;[<a target="_blank" href="{$arag_base_url}modpub/theme_manager/uploaded/{$style.value}">_("current picture")</a>]
                    {/if}
                 </td>
             </tr>
             {/foreach}
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value={quote}_("Save"){/quote} /></td>
            </tr>
         </table>

    {/arag_block}
{/arag_form}
{/arag_block}
