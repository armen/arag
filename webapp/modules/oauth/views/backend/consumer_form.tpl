{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_block}

    {arag_validation_errors}

    {arag_block template="info"}
        {capture assign="info_msg"}_("Fields marked with a %s are required."){/capture}
        {asterisk message=$info_msg}
    {/arag_block}

    {arag_form uri=$uri method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td align="{right}" width="120">{asterisk}_("Requester Name"):</td>
        <td><input type="text" name="requester_name" value="{$requester_name|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="120">{asterisk}_("Requester Email"):</td>
        <td><input type="text" name="requester_email" value="{$requester_email|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="120">_("Callback URI"):</td>
        <td><input type="text" name="callback_uri" value="{$callback_uri|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="120">_("Allication URI"):</td>
        <td><input type="text" name="application_uri" value="{$application_uri|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="120">_("Allication Title"):</td>
        <td><input type="text" name="application_title" value="{$application_title|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="120">_("Allication Desc"):</td>
        <td><input type="text" name="application_desc" value="{$application_desc|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td align="{right}" width="120">_("Allication Notes"):</td>
        <td><input type="text" name="application_notes" value="{$application_notes|smarty:nodefaults|default:null}" /></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            {if $action == 'register'}
                <input type="submit" value={quote}_("Register"){/quote} />
            {elseif $action == 'edit'}
                <input type="hidden" name="id" value="{$id}" />
                <input type="hidden" name="consumer_key" value="{$consumer_key}" />
                <input type="hidden" name="consumer_secret" value="{$consumer_secret}" />
                <input type="submit" value={quote}_("Edit"){/quote} />
            {/if}
            <input type="reset" value={quote}_("Reset"){/quote} />
        </td>
    </tr>
    </table>
    {/arag_form}

{/arag_block}
