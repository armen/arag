{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_load_script src="scripts/mootools.js"}
{assign var=comments value=$component->getComments()}
{assign var=title value=$component->getTitle()}

{if count($comments)}
    {arag_block}
        <div class="comments">
            <h3>{$title}</h3>
            {foreach from=$comments item=_comment key=key}
                <div class="comment">
                    <div class="comment_posted">
                        {capture assign="posted"}_("#%d. %s on %s"){/capture}
                        {capture assign="date"}{kohana_helper function="format::date" date=$_comment->create_date}{/capture}
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
    {/arag_block}
{/if}

{arag_block}
    {arag_validation_errors}
    {arag_form uri=$component->getUri() method="post" enctype="multipart/form-data"}
    <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{right}" width="100">
                _("Comment"):
            </td>
            <td align="{left}">
                <textarea name="comment" rows="7" cols="15">{$comment|smarty:nodefaults|default:null}</textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="hidden" name="key" value="{$component->getKey()}" />
            </td>
        </tr>
        <tr>
            <td align="{right}">
                _("Attachment"):
            </td>
            <td align="{left}">
                <div style="float:{left};">
                    <div id="attachment" style="display:none;">
                        <input type="file" name="attachments[]" />
                    </div>
                    <div id="attachments_container">
                        <input type="file" name="attachments[]" />
                    </div>
                </div>
                <div style="float:{left};">
                    <input type="button" value="+" onclick="$('attachment').clone().injectAfter('attachments_container').set('style', 'display:block;');" />
                </div>
                <div style="clear:both;"></div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="{left}">
                <input type="submit" value={quote}_("Submit"){/quote} />
                <input type="reset" value={quote}_("Reset"){/quote} />
            </td>
        </tr>
    </table>
    {/arag_form}
{/arag_block}
