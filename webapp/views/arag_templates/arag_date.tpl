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
                            cal = {/literal}{$id}{literal}Cal;
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
        });
        {/literal}
    </script>
{/if}
{if $multiple == "false"}
    <input id="{$id}" name="{$name}" type="text" value="{$value}" onfocus="javascript:Calendar.show;" />
{else}
    <input id="{$id}" name="{$name}" type="hidden" value="{$multiple_value}" />
{/if}
{if $toggle}
    <img src="{$arag_base_url}images/date/toggle.png" width="22" height="22" id="{$id}_toggle" alt={quote}_("Change calendar system"){/quote} style="cursor: pointer;" />
    <input id="{$id}_type" name="type_{$name}" type="hidden" value="{$type}" />
{/if}
<img src="{$arag_base_url}images/date/date.png" width="22" height="22" id="{$id}_cal" alt={quote}_("Calendar"){/quote} style="cursor: pointer;" />
<script type="text/javascript">
    var id              = "{$id}";
    var type            = "{$type}";
    var format          = "{$format}";
    var button          = "{$id}_cal";
    var multiple        = {if $multiple == "true"}[{foreach from=$value item="date"}Date.parseDate("{$date}", format, type),{/foreach}]{else}false{/if};
    var {$id}validDates = {$valid_dates|smarty:nodefaults};
    var {$id}Cal        = Calendar.setup(
    {literal}
        {
            inputField      : multiple ? null : id,       // ID of the input field
            dateType        : type,     // the date format
            daFormat        : format,
            button          : button,   // ID of the button
            weekNumbers     : false,    // WeekNumbers
            autoShowOnFocus : true,     // Autoshowonfocus
            multiple        : multiple,
            onClose         : function(cal) {

            	if (cal.multiple) {
                    var element = document.getElementById(id);
                    element.value = null;
            		for (var i in cal.multiple) if (cal.multiple[i] instanceof Date) {
                        element.value += cal.multiple[i].print(cal.dateFormat, cal.dateType, cal.langNumbers)+',';
                    }
                }
                cal.hide();
            },
            disableFunc     : function(date) {

                var validDates = {/literal}{$id}validDates;{literal}

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
