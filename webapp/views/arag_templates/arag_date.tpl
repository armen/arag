{arag_load_script src="scripts/JalaliJSCalendar/calendar.js"}
{arag_load_script src="scripts/JalaliJSCalendar/lang/calendar-$lang.js"}
{arag_load_script src="scripts/JalaliJSCalendar/calendar-setup.js"}
{arag_load_script src="scripts/JalaliJSCalendar/jalali.js"}
{if $toggle}
    {arag_load_script src="scripts/mootools/core.js"}
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
    {if !$multiple}
    <input id="{$id}" name="{$name}" type="text"  onfocus="javascript:Calendar.show;" value="{$value|smarty:nodefaults|default:''}" {$size|smarty:nodefaults} />
    {else}
        <input id="{$id}" name="{$name}" type="hidden" value="{$init_values|smarty:nodefaults|default:''}" />
    {/if}
    {if !$multiple}
        {capture assign=title}_("Sample Format"){/capture}
        {arag_tooltip activator="$id" title=$title x=$tip_x y=$tip_y}
            {$format_sample}
        {/arag_tooltip}
    {/if}
    <img src="{$arag_base_url}images/date/date.png" width="22" height="22" id="{$id}_cal" alt={quote}_("Calendar"){/quote} style="cursor: pointer;" />
    {capture assign=title}_("Select Date"){/capture}
    {arag_tooltip activator="`$id`_cal" title=$title x=$tip_x y=$tip_y}
        _("Click here to select date")
    {/arag_tooltip}
{/if}
{if $toggle}
    <img src="{$arag_base_url}images/date/toggle.png" width="22" height="22" id="{$id}_toggle" alt={quote}_("Change calendar system"){/quote} style="cursor: pointer;" />
    <input id="{$id}_type" name="type_{$name}" type="hidden" value="{$type}" />
    {capture assign=title}_("Toggel Calendar Type"){/capture}
    {arag_tooltip activator="`$id`_toggle" title=$title}
        _("Click here to toggle calendar type")
    {/arag_tooltip}
{/if}
<script type="text/javascript">
    var type                = "{$type}";
    var format              = "{$format}";
    var button              = "{$id}_cal";
    var parent              = {if $parent}"{$parent}"{else}null{/if};
    var disable_past        = {if $disable_past}true{else}false{/if};
    var multiple            = {if $multiple}[{foreach from=$value item="date" name="dates"}new Date('{$date} GMT'){if !$smarty.foreach.dates.last},{/if}{/foreach}]{else}false{/if};
    var {$prefix}datesToValidate = {$dates_to_validate|smarty:nodefaults};
    var {$prefix}Cal             = Calendar.setup(
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

                if (disable_past) {
                    var today = new Date();
                    today     = new Date(today.getTime()-(today.getTimezoneOffset()*60*1000));

                    if (date.getTime() < today.getTime()) {
                        return true;
                    }
                }

                var datesToValidate = {/literal}{$prefix}datesToValidate;{literal}

                if (datesToValidate) {
                    for (valid_date in datesToValidate) {
                        valid_date = new Date(datesToValidate[valid_date]*1000);
                        valid_date = new Date(valid_date.getTime()-(valid_date.getTimezoneOffset()*60*1000));
                        if (valid_date.getYear() == date.getYear() && valid_date.getMonth() == date.getMonth() && valid_date.getDate() == date.getDate()) {
                            {/literal}
                                return {if $is_valid}false{else}true{/if};
                            {literal}
                        }
                    }
                    {/literal}
                        return {if $is_valid}true{else}false{/if};
                    {literal}
                }
                {/literal}
                    return {if $is_valid}true{else}false{/if};
                {literal}
            }
        });
    {/literal}
</script>
