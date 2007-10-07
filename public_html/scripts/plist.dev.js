// {{{ toggleCheckboxesStatus
function toggleCheckboxesStatus(status, namespace)
{
    $$('#plist_'+namespace+' input[type="checkbox"]').setProperty('checked', status);
}
// }}}
// {{{ toggleListCheckbox
function toggleListCheckbox(el)
{
    el.setProperty('checked', !el.getProperty('checked'));
}
// }}}
// {{{ listForward
function listForward(el, namespace)
{
    var action = (el.getTag() == 'a')?el.href:
                 ((el.getTag() == 'select')?el.options[el.selectedIndex].value:Null);

    $('plist_'+namespace).setProperty('action', action).submit();

    if (el.getTag() == 'a') {
        // Set the href to none, important to set unless it does not work
        el.href = '#none';

    } else if (el.getTag() == 'select') {
        // Set selected index to 0 then if user hit the back will see defaul value
        toggleCheckboxesStatus(false, namespace);                    
        el.selectedIndex = 0;                    
    }

    // Uncheck checkboxes then if user hit the back will see defaul value
    toggleCheckboxesStatus(false, namespace);                
}
// }}}
// {{{ listMouseEvent
function listMouseEvent(el, event, normal)
{
    var sHilight = 'plist_shilight';
    var hilight  = 'plist_hilight';

    if (event == 'over' && el.className != sHilight) {
        el.className = hilight;

    } else if (event == 'out' && el.className != sHilight) {
        el.className = normal;

    } else if (event == 'click') {
        if (el.className == sHilight) {
            el.className = normal;
        } else {
            el.className = sHilight;
        }
    }
}
// }}}
