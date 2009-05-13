{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_load_script src="scripts/mootools.js"}
<script language="javascripts" type="text/javascript">
{literal}
function EnableDisable(val)
{
    if (val=='availabe') {
        $('available_routes').removeProperty('disabled');
        $('available_routes').focus();
        $('new_route').setProperty('disabled', 'disabled');
    }
    if (val=='new') {
        $('available_routes').setProperty('disabled', 'disabled');
        $('new_route').removeProperty('disabled');
        $('new_route').focus();
    }
}
{/literal}
</script>

{arag_block}
    {arag_validation_errors}
    {arag_form uri="staticpages/backend/create"}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">_("Use availabe routes?")
                    <input type="radio" id="available_route_radio" name="route" onchange="return EnableDisable('availabe')" />
                </td>
                <td>
                    <select name="available_route" id="available_routes" disabled="disabled">
                        <option>&nbsp;</option>
                        {html_options options=$available_routes }
                    </select>
                </td>
            </tr>
            <tr>
                <td align="{right}">_("Make anew route?")
                    <input type="radio" id="new_route_radio" name="route" onchange="return EnableDisable('new')"  />
                </td>
                <td><input type="text" name="new_route" id="new_route" disabled="disabled" /></td>
            </tr>
        </table>

        <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{left}">
                _("Subject"):{asterisk}<input type="text" name="subject" value="{$subject|smarty:nodefaults|default:null}" />
            </td>
        </tr>
        <tr>
            <td align="{left}">
                {arag_rte name="page" value=$page|smarty:nodefaults|default:null}
            </td>
        </tr>
        <tr>
            <td align="{left}">
                <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
            </td>
        </tr>
    </table>
    {*For validation it is require*}
    <input type="hidden" name="routes" value="1">
    {/arag_form}
{/arag_block}
