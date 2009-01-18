// {{{ toggleCheckboxesStatus
function toggleCheckboxesStatus(status, namespace)
{
    $$('#plist_'+namespace+' input[type="checkbox"]').set('checked', status);
}
// }}}
// {{{ toggleListCheckbox
function toggleListCheckbox(el)
{
    el.set('checked', !el.get('checked'));
}
// }}}
// {{{ listForward
function listForward(el, namespace)
{
    el = $(el);
    var action = (el.get('tag') == 'a')?el.href:
                 ((el.get('tag') == 'select')?el.options[el.selectedIndex].value:Null);

    $('plist_'+namespace).set('action', action).submit();

    if (el.get('tag') == 'a') {
        // Set the href to none, important to set unless it does not work
        el.href = '#none';

    } else if (el.get('tag') == 'select') {
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
