{arag_load_script src="scripts/JalaliJSCalendar/calendar.js"}
{arag_load_script src="scripts/JalaliJSCalendar/lang/calendar-$lang.js"}
{arag_load_script src="scripts/JalaliJSCalendar/calendar-setup.js"}
{arag_load_script src="scripts/JalaliJSCalendar/jalali.js"}
{if $toggle}
    {arag_load_script src="scripts/mootools.js"}
    <script type="text/javascript">
        {literal}
        window.addEvent('domready', function() {
            $('{/literal}{$name}{literal}_toggle').addEvent('click', function() {
                            cal = {/literal}{$name}{literal}Cal;
                            var dateType = cal.dateType;
                            if (dateType == 'jalali') {
                                cal.setDateType('gregorian');
                                $('{/literal}{$name}{literal}_type').setProperty('value','gregorian');
                            } else {
                                cal.setDateType('jalali');
                                $('{/literal}{$name}{literal}_type').setProperty('value','jalali');
                            }
                            $('{/literal}{$name}{literal}').setProperty('value', '');
                            cal.recreate();
                        });
        });
        {/literal}
    </script>
{/if}
<input id="{$name}" name="{$name}" type="text" value="{$value}" onfocus="javascript:Calendar.show;" />
<img src="{$arag_base_url}images/date/date.png" width="22" height="22" id="{$name}_cal" alt={quote}_("Calendar"){/quote} style="cursor: pointer;" />
{if $toggle}
    <img src="{$arag_base_url}images/date/toggle.png" width="22" height="22" id="{$name}_toggle" alt={quote}_("Change calendar system"){/quote} style="cursor: pointer;" />
    <input id="{$name}_type" name="{$name}_type" type="hidden" value="{$type}" />
{/if}
<script type="text/javascript">
    var id     = "{$name}";
    var type   = "{$type}";
    var format = "{$format}";
    var button = "{$name}_cal";
    var {$name}Cal = Calendar.setup(
    {literal}
        {
            inputField      : id,       // ID of the input field
            dateType        : type,     // the date format
            format          : format,
            button          : button,   // ID of the button
            weekNumbers     : false,    // WeekNumbers
            autoShowOnFocus : true,     // Autoshowonfocus
        });
    {/literal}
</script>
