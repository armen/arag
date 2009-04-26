{arag_load_script src="scripts/JalaliJSCalendar/calendar.js"}
{arag_load_script src="scripts/JalaliJSCalendar/lang/calendar-$lang.js"}
{arag_load_script src="scripts/JalaliJSCalendar/calendar-setup.js"}
{arag_load_script src="scripts/JalaliJSCalendar/jalali.js"}
{if $toggle}
    {arag_load_script src="scripts/mootools.js"}
    <script type="text/javascript">
        {literal}
        window.addEvent('domready', function() {
            $('{/literal}{$id}{literal}_toggle').addEvent('click', function() {
                            cal = {/literal}{$prefix}{literal}Cal;
                            var dateType = cal.dateType;
                            if (dateType == 'jalali') {
                                cal.setDateType('gregorian');
                                $('{/literal}{$id}{literal}_type').setProperty('value','gregorian');
                            } else {
                                cal.setDateType('jalali');
                                $('{/literal}{$id}{literal}_type').setProperty('value','jalali');
                            }
                            $('{/literal}{$id}{literal}').setProperty('value', '');
                            cal.recreate();
                        });
            $('{/literal}{$id}{literal}').addEvent('focus', function() {
                                if ($('{/literal}{$id}{literal}').get('value') == '{/literal}{$format_sample}{literal}')
                                    {$('{/literal}{$id}{literal}').setProperty('value', '');
                                }
                        });
            $('{/literal}{$id}{literal}').addEvent('blur', function() {
                                if (!$('{/literal}{$id}{literal}').get('value'))
                                    {$('{/literal}{$id}{literal}').setProperty('value', '{/literal}{$format_sample}{literal}');
                                }
                        });
        });
        {/literal}
    </script>
{/if}
{if !$parent}
    {if $multiple == "false"}
    <input id="{$id}" name="{$name}" type="text"  onfocus="javascript:Calendar.show;" value="{$value|smarty:nodefaults|default:''}" {$size|smarty:nodefaults} />
    {else}
        <input id="{$id}" name="{$name}" type="hidden" value="{$multiple_value}" />
    {/if}
    {if $multiple == "false"}
        {capture assign=title}_("Format"){/capture}
        {arag_tooltip activator=$id title=$title}
            {$format_sample}
        {/arag_tooltip}
    {/if}
    <img src="{$arag_base_url}images/date/date.png" width="22" height="22" id="{$id}_cal" alt={quote}_("Calendar"){/quote} style="cursor: pointer;" />
    {capture assign=title}_("Select Date"){/capture}
    {arag_tooltip activator="`$id`_cal" title=$title}
        _("Click here to select date")
    {/arag_tooltip}
{/if}
{if $toggle}
	<img src="{$arag_base_url}images/date/toggle.png" width="22" height="22" id="{$id}_toggle" alt={quote}_("Change calendar system"){/quote} style="cursor: pointer;" />
    {capture assign=title}_("Toggel Calendar"){/capture}
    {arag_tooltip activator="`$id`_toggle" title=$title}
        _("Click here to toggle calendar type")
    {/arag_tooltip}
	<input id="{$id}_type" name="type_{$name}" type="hidden" value="{$type}" />
{/if}

<script type="text/javascript">
    var type                = "{$type}";
    var format              = "{$format}";
    var button              = "{$id}_cal";
    var parent              = {if $parent}"{$parent}"{else}null{/if};
    var multiple            = {if $multiple == "true"}[{foreach from=$value item="date" name="dates"}Date.parseDate("{$date}", format, type){if !$smarty.foreach.dates.last},{/if}{/foreach}]{else}false{/if};
    var {$prefix}validDates = {$valid_dates|smarty:nodefaults};
    var {$prefix}Cal        = Calendar.setup(
    {literal}
        {
            inputField      : multiple ? false : "{/literal}{$id}{literal}",       // ID of the input field
            dateType        : type,     // the date format
            daFormat        : format,
            button          : button,   // ID of the button
            weekNumbers     : false,    // WeekNumbers
            autoShowOnFocus : true,     // Autoshowonfocus
            multiple        : multiple,
            flat            : parent,
            onClose         : function(cal) {

                if (cal.multiple) {
                    var element   = document.getElementById("{/literal}{$id}{literal}");
                    element.value = '';
                    for (var i in cal.multiple) if (cal.multiple[i] instanceof Date) {
                        element.value += cal.multiple[i].print(cal.dateFormat, cal.dateType, cal.langNumbers)+',';
                    }
                }
                cal.hide();
            },
            disableFunc     : function(date) {

                var validDates = {/literal}{$prefix}validDates;{literal}

                if (validDates) {
                    for (i = 0; i < validDates.length; i++) {
                        if (validDates[i] == date.print(format, type, false)) {
                            return false;
                        }
                    }
                    return true;
                }

                return false;
            }
        });
    {/literal}
</script>
