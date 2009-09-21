{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_block}
    {if $queue_is_not_writeable|smarty:nodefaults|default:null}
        {arag_block template="error"}
            {capture assign="error"}_("Queue is not writeable at '%s'"){/capture}
            {$error|sprintf:$queue}
        {/arag_block}
    {else}
        {arag_block template="info"}
            {if isset($sent|smarty:nodefaults)}
                {capture assign="info"}_("%d message(s) has been sent."){/capture}
                {$info|sprintf:$sent}
            {else}
                {capture assign="info"}_("%d message(s) has been processed."){/capture}
                {$info|sprintf:$processed}
            {/if}
        {/arag_block}
        {arag_validation_errors}
        {arag_block}
            {arag_form uri="message_queue/backend/statistics/wipe" method="post"}
                <input type="hidden" name="channel" value="{$channel}" />
                {if isset($sent|smarty:nodefaults)}
                    _("Wipe sent messages older than")
                {else}
                    _("Wipe processed messages older than")
                {/if}
                <input type="text" name="older_than" size="3" />
                _("minute(s)")
                <input type="submit" value={quote}_("Wipe"){/quote} />
            {/arag_form}
        {/arag_block}
        {arag_plist name="messages"}
    {/if}
{/arag_block}
