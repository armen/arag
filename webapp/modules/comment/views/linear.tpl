{* Smarty *}
{*  
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
    File: $Id$
*}

{assign var=comments value=$component->getComments()}
{assign var=title value=$component->getTitle()}

{if count($comments)}
    <div class="comments">
        <h3>{$title}</h3>
        {foreach from=$comments item=_comment key=key}
            <div class="comment">
                <div class="comment_posted">
                    {capture assign="posted"}_("#%d. %s on %s"){/capture}
                    {capture assign="date"}{$_comment->create_date|date_format:'%A, %B %e, %Y %H:%M:%S'}{/capture}
                    {counter assign="counter"}
                    {if empty($_comment->homepage|smarty:nodefaults)}
                        {$posted|sprintf:$counter:$_comment->name:$date}
                    {else}
                        {kohana_helper function="html::anchor" uri=$_comment->homepage title=$posted|sprintf:$counter:$_comment->name:$date}
                    {/if}
                </div>
                <div class="comment_body">
                    {$_comment->comment|nl2br|smarty:nodefaults}
                </div>
            </div>
        {/foreach}
    </div>
{/if}

{arag_block}
    {arag_block template="blank"}    
        {arag_validation_errors}

        {arag_block template="info"}
            {capture assign="info_msg"}_("Fields marked with a %s are required."){/capture}
            {asterisk message=$info_msg}
        {/arag_block}

        {arag_form uri=$component->getPostUri() method="post"}
        <table border="0" dir="{dir}" width="100%">
        {if !$component->onlyComment()}
            <tr>
                <td align="{right}" width="100">_("Name"){asterisk}:</td>
                <td><input type="text" name="name" value="{$name|smarty:nodefaults|default:null}" /></td>
            </tr>
            <tr>
                <td align="{right}" width="100">_("Email"):</td>
                <td><input type="text" name="email" value="{$email|smarty:nodefaults|default:null}" /></td>
            </tr>
            <tr>
                <td align="{right}" width="100">_("Home page"):</td>
                <td><input type="text" name="homepage" value="{$homepage|smarty:nodefaults|default:null}" /></td>
            </tr>
        {/if}
        <tr>
            <td align="{right}" width="100">_("Comment"){asterisk}:</td>
            <td><textarea name="comment" rows="7" cols="15">{$comment|smarty:nodefaults|default:null}</textarea></td>
        </tr>        
        <tr>
            <td>&nbsp;</td>
            <td>
                {if $component->onlyComment()}
                    <input type="hidden" name="name" value="{$name|smarty:nodefaults|default:'Anonymous'}" />
                    <input type="hidden" name="email" value="{$email|smarty:nodefaults|default:null}" />
                {/if}            
                <input type="hidden" name="module" value="{$component->getModule()}" />
                <input type="hidden" name="reference_id" value="{$component->getReferenceId()}" />
                <input type="submit" value={quote}_("Submit"){/quote} />
                <input type="reset" value={quote}_("Reset"){/quote} />            
            </td>
        </tr>
        </table>
        {/arag_form}
    {/arag_block}
{/arag_block}
