{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: edit.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_block}
    {if $message}
        {arag_block template="error"}
            {$message}
        {/arag_block}
    {/if}
    {if $error_message}
        {arag_block template="warning"}
            {$error_message}
        {/arag_block}
    {/if}
    {if $is_sent}
        {arag_block template="info"}
            _("Please follow the instruction contained in an email sent to your email address, to complete the proccess.")
        {/arag_block}
    {elseif !$error_message}
        {arag_block template="error"}
            _("There was a problem in sending email. Sorry for the inconvenient but we are not able to do your request now. Please try later...")
        {/arag_block}
    {/if}
{/arag_block}
