function toggleCheckboxesStatus(status,namespace){$$('#plist_'+namespace+' input[type="checkbox"]').set('checked',status)}function toggleListCheckbox(el){el.set('checked',!el.get('checked'))}function listForward(el,namespace){el=$(el);var action=(el.get('tag')=='a')?el.href:((el.get('tag')=='select')?el.options[el.selectedIndex].value:Null);$('plist_'+namespace).set('action',action).submit();if(el.get('tag')=='a'){el.href='#none'}else if(el.get('tag')=='select'){toggleCheckboxesStatus(false,namespace);el.selectedIndex=0}toggleCheckboxesStatus(false,namespace)}function listMouseEvent(el,event,normal){var sHilight='plist_shilight';var hilight='plist_hilight';if(event=='over'&&el.className!=sHilight){el.className=hilight}else if(event=='out'&&el.className!=sHilight){el.className=normal}else if(event=='click'){if(el.className==sHilight){el.className=normal}else{el.className=sHilight}}}
