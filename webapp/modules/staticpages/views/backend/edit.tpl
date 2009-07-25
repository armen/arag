{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{arag_load_script src="scripts/mootools/core.js"}
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
{if array_key_exists($route, $available_routes)}
    {capture assign=available_value}{$route}{/capture}
{else}
    {capture assign=new_value}{$route}{/capture}
{/if}
{arag_block}
    {arag_validation_errors}
    {arag_form uri="staticpages/backend/edit/$id"}
    <table border="0" dir="{dir}">
        <tr>
            <td align="{right}">_("Use availabe routes?")
                <input type="radio" id="available_route_radio" name="route"
                 onchange="return EnableDisable('availabe')"
                 {if $available_value|smarty:nodefaults|default:null} checked="checked"{/if} />
            </td>
            <td>
            <select name="available_route" id="available_routes"
                        {if !$available_value|smarty:nodefaults|default:null}disabled="disabled"{/if}>
                    <option>&nbsp;</option>
                    {html_options options=$available_routes
                                  selected=$available_value|smarty:nodefaults|default:null}
            </select>
            </td>
        </tr>
        <tr>
            <td align="{right}">_("Make anew route?")
                <input type="radio" id="new_route_radio" name="route"
                onchange="return EnableDisable('new')"
                {if $new_value|smarty:nodefaults|default:null} checked="checked"{/if} />
            </td>
            <td><input type="text" name="new_route" id="new_route"
                       {if !$new_value|smarty:nodefaults|default:null}disabled="disabled"{/if}
                       value="{$new_value|smarty:nodefaults|default:null}" /></td>
        </tr>
    </table>
    <table border="0" dir="{dir}" width="100%">
        <tr>
            <td align="{left}">
                _("Subject"):{asterisk}<input type="text" name="subject" value="{$subject}" />
            </td>
        </tr>
        <tr>
            <td align="{left}">
                {arag_rte name="page" value=$page|smarty:nodefaults}
                <input type="hidden" name="id" value="{$id}" />
            </td>
        </tr>
        <tr>
            <td align="{left}">
                <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
            </td>
        </tr>
    </table>
    {/arag_form}
{/arag_block}
