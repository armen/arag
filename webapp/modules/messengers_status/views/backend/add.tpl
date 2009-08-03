{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_load_script src="scripts/mootools/core.js"}

{arag_block}

    {arag_validation_errors}

    {if $saved}
        {arag_block align="left" template="info"}
            _("Settings are updated successfuly!")
        {/arag_block}
    {/if}

    {arag_form uri="messengers_status/backend/messengers/add" method="post"}
    <table border="0" dir="{dir}" width="100%">
    <tr>
        <td width="700">
            <div id="ids" style="display:none;">
                <table border="0" dir="{dir}" width="100%">
                <tr>
                    <td align="{right}" width="50">_("ID"):{asterisk}</td>
                    <td width="150"><input type="text" name="id[]" dir="ltr" disabled="disabled" /></td>
                    <td align="{right}" width="50">_("Subject"):</td>
                    <td width="200"><input type="text" size="30" name="subject[]" disabled="disabled" /></td>
                    <td align="{right}" width="50">_("Type"):{asterisk}</td>
                    <td width="120">{html_options options=$types name="type[]" attribute="diabled=\"disabled\""}</td>
                    <td><input type="button" value="-" style="width:24px;height:20px" onclick="this.getParent().getParent().getParent().getParent().empty();" /></td>
                </tr>
                </table>
            </div>
            <div id="ids_container">
                {if !isset($id|smarty:nodefaults)}
                <table border="0" dir="{dir}" width="100%">
                <tr>
                    <td align="{right}" width="50">_("ID"):{asterisk}</td>
                    <td width="150"><input type="text" name="id[]" value="{$id|smarty:nodefaults|default:null}" dir="ltr" /></td>
                    <td align="{right}" width="50">_("Subject"):</td>
                    <td width="200"><input type="text" size="30" name="subject[]" value="{$subject|smarty:nodefaults|default:null}" /></td>
                    <td align="{right}" width="50">_("Type"):{asterisk}</td>
                    <td width="120">{html_options options=$types name="type[]" selected=$type|smarty:nodefaults|default:null}</td>
                    <td><input type="button" value="+" style="width:24px;height:20px"
                               onclick="$('ids').clone().inject('ids_container').set('style', 'display:block;').getElements('*').removeProperty('disabled');" /></td>
                </tr>
                </table>
                {else}
                    {foreach name="ids" from=$id item=item key=key}
                    <table border="0" dir="{dir}" width="100%">
                    <tr>
                        <td align="{right}" width="50">_("ID"):{asterisk}</td>
                        <td width="150"><input type="text" name="id[]" value="{$item|smarty:nodefaults|default:null}" dir="ltr" /></td>
                        <td align="{right}" width="50">_("Subject"):</td>
                        <td width="200"><input type="text" size="30" name="subject[]" value="{$subject[$key]|smarty:nodefaults|default:null}" /></td>
                        <td align="{right}" width="50">_("Type"):{asterisk}</td>
                        <td width="120">{html_options options=$types name="type[]" selected=$type[$key]|smarty:nodefaults|default:null}</td>
                        {if $smarty.foreach.ids.first}
                            <td><input type="button" value="+" style="width:24px;height:20px"
                                       onclick="$('ids').clone().inject('ids_container').set('style', 'display:block;').getElements('*').removeProperty('disabled');" /></td>
                        {else}
                            <td><input type="button" value="-" style="width:24px;height:20px" onclick="this.getParent().getParent().getParent().getParent().empty();" /></td>
                        {/if}
                    </tr>
                    </table>
                    {/foreach}
                {/if}
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="{right}">
            <input type="submit" value={quote}_("Save"){/quote} />
            <input type="reset" value={quote}_("Reset"){/quote} />
        </td>
        <td>&nbsp;</td>
    </tr>
    </table>
    {/arag_form}

{/arag_block}
