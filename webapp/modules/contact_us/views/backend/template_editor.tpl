{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}
{arag_block}
    {arag_form uri=$uri method="post"}
        <table width="100%" border="0" dir="{dir}">
            <tr>
                <td>_("Template"):</td>
                <td>
                    {arag_rte name="template" value=$template|smarty:nodefaults|default:null}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="submit" value="Submit" />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
