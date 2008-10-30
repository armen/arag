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

   {arag_form uri=help/backend/add method="post"}
   <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{right}" width="100">_("Title"):</td>
            <td><input type="text" name="title" value="" /></td>
        </tr>
        <tr>
            <td align="{right}">_("Message"):</td>
            <td>
                {arag_rte name="message"}
            </td>
        </tr>
        <tr>
            <td align="{right}">_("Dialog type"):</td>
            <td>
                {html_options name="type" options=$dialogs}
            </td>
        </tr>
        <tr>
            <td align="{right}">_("Allowed groups"):</td>
            <td>
                {foreach from=$groups item='group'}
                    <input id="group_{$group.id}" type="checkbox" name="groups[{$group.id}]" checked="checked" />
                    <label for="group_{$group.id}">
                        {$group.name}
                    <label>
                    <br />
                {/foreach}
            </td>
        </tr>
        <tr>
            <td align="{right}">&nbsp;</td>
            <td>
                <input type="hidden" name="uri" value="{$uri}">
                <input type="submit" name="add" value={quote}_("Save"){/quote}>
            </td>
        </tr>
    </table>
    {/arag_form}
{/arag_block}