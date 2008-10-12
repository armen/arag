{arag_load_script src="scripts/JalaliJSCalendar/calendar.js"}
{arag_load_script src="scripts/JalaliJSCalendar/lang/calendar-$lang.js"}
{arag_load_script src="scripts/JalaliJSCalendar/calendar-setup.js"}
{arag_load_script src="scripts/JalaliJSCalendar/jalali.js"}

<input id="{$name}" name="{$name}" type="text" onfocus="javascript:Calendar.show;" />
<img src="{$arag_base_url}images/date/date.png" width="22" height="22" id="{$name}_cal" alt={quote}_("Calendar"){/quote} style="cursor: pointer;" title={quote}_("Calendar"){/quote} />

<script type="text/javascript">
    var id     = "{$name}";
    var type   = "{$type}";
    var format = "{$format}";
    var button = "{$name}_cal";
    {literal}
        var cal = Calendar.setup(
        {
            inputField      : id,     // ID of the input field
            dateType        : type,     // the date format
            format          : format,
            button          : button,   // ID of the button
            weekNumbers     : false,    // WeekNumbers
            autoShowOnFocus : true,     // Autoshowonfocus
        });
    {/literal}
</script>