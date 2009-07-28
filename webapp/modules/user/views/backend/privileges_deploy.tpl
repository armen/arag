{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_load_script src="scripts/mootools/core.js"}
{arag_load_script src="scripts/mootools-more.js"}
{literal}
<script type="text/javascript">
    var fetchData = function(sourceUrl, container) {
        var request = new Request.JSON({
            url: sourceUrl,
            onComplete: function(jsonObj) {
                updateSelect(jsonObj.entries, container);
            }
        }).send();
    }

    var updateSelect = function(options, container) {
        container.empty();
        select    = new Element('select', {'name':'groups[]'});
        optionTag = new Element('option', {'value':'_all_', 'html':'_("All")'});
        optionTag.inject(select);
        options.each(function(option) {
            new Element('option', {'value':option.key, 'html':option.value}).inject(select);
        });
        select.inject(container);
    }

    var getGroupsBaseUrl = '{/literal}{kohana_helper function="url::site" uri="user/backend/applications/get_groups_of"}/';
    {literal}
    function updateGroups(application) {
        var appname          = application.getSelected().getProperty('value')
        var groups_container = application.getParent().getElements('span');
        var getGroupsUrl     = getGroupsBaseUrl + appname;

        fetchData(getGroupsUrl, groups_container[0]);
    }

    window.addEvent('domready', function() {
        updateGroups($('applications'));
    });

    {/literal}
</script>
{arag_validation_errors}
{arag_block}
    {if $success}
        {arag_block template="info"}
            _("Privileges has been deploied successfully!")
        {/arag_block}
    {/if}
    {arag_form uri="user/backend/applications/privileges_deploy"}
        <table border="0" dir="{dir}">
            <tr>
                <td align="{right}">
                    _("Applications"):{asterisk}
                </td>
                <td align="{left}">
                    <div style="float:{left};">
                        <div id="application" style="display:none;">
                            <div style="clear:both;"></div>
                            <select name="applications[]" onchange="updateGroups(this)" disabled="disabled">
                                <option value="_all_">_("All")</option>
                                {html_options options=$app_labels}
                            </select>
                            <span><img src="{$arag_base_url}images/misc/loading.gif" width="16" height="16" alt={quote}_("Loading"){/quote} /></span>
                            <input type="button" value="-" style="width:24px;height:20px" onclick="this.getParent().empty();" />
                        </div>
                        <div id="applications_container">
                            {if !isset($applications|smarty:nodefaults)}
                            <select id="applications" onchange="updateGroups(this)" name="applications[]">
                                <option value="_all_">_("All")</option>
                                {html_options options=$app_labels}
                            </select>
                            <span><img src="{$arag_base_url}images/misc/loading.gif" width="16" height="16" alt={quote}_("Loading"){/quote} /></span>
                            <input type="button" value="+" style="width:24px;height:20px"
                                   onclick="select = $('application').clone().inject('applications_container').set('style', 'display:block;').getElements('select')[0].removeProperty('disabled'); updateGroups(select);" />
                            {else}
                                {foreach name="applications" from=$applications key=key item=application}
                                    <div style="clear:both;">
                                        <select name="applications[]">
                                            <option value="_all_" {if $application == '_all_'}selected="selected"{/if}>_("All")</option>
                                            {html_options options=$app_labels selected=$application}
                                        </select>
                                        <span>
                                            <select name="groups[]">
                                                {html_options options=$app_groups.$application selected=$groups.$key}
                                            </select>
                                        </span>
                                        {if $smarty.foreach.applications.first}
                                            <input type="button" value="+" style="width:24px;height:20px"
                                                   onclick="select = $('application').clone().inject('applications_container').set('style', 'display:block;').getElements('select')[0].removeProperty('disabled'); updateGroups(select);" />
                                        {else}
                                            <input type="button" value="-" style="width:24px;height:20px" onclick="this.getParent().empty();" />
                                        {/if}
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Parent"):{asterisk}
                </td>
                <td>
                    {html_options name="parent" options=$parent_labels selected=$parent|smarty:nodefaults|default:""}
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Label"):{asterisk}
                </td>
                <td>
                    <input type="text" name="label"  value="{$label|smarty:nodefaults|default:""}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                    _("Privilege"):{asterisk}
                </td>
                <td>
                    <input type="text" name="privilege" value="{$privilege|smarty:nodefaults|default:""}" />
                </td>
            </tr>
            <tr>
                <td align="{right}">
                </td>
                <td align="{left}">
                    <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                </td>
            </tr>
        </table>
    {/arag_form}
{/arag_block}
