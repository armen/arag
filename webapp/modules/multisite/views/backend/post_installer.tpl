{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}

{arag_validation_errors}

{if $installed}
    {if $email_is_sent}
        {arag_block align="left" template="info"}
            {arag_block template="empty"}_("New application added successfuly!"){/arag_block}
            {arag_block template="empty"}_("An email sent successfuly to the admin of the new application!"){/arag_block}
        {/arag_block}
    {else}
        {arag_block align="left" template="info"}
            _("New application added successfuly!")
        {/arag_block}
        {arag_block align="left" template="warning"}
            _("There was a problem in sending email to the admin of the new application!")
        {/arag_block}
    {/if}
{/if}

{arag_block}
    {arag_block template="blank"}
        _("Please Implement Post Installer :)")
    {/arag_block}
{/arag_block}
