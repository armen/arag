function toggleCheckboxesStatus(status,namespace){$$('#plist_'+namespace+' input[type="checkbox"]').setProperty('checked',status)}function toggleListCheckbox(el){el.setProperty('checked',!el.getProperty('checked'))}function listForward(el,namespace){var action=(el.getTag()=='a')?el.href:((el.getTag()=='select')?el.options[el.selectedIndex].value:Null);$('plist_'+namespace).setProperty('action',action).submit();if(el.getTag()=='a'){el.href='#none'}else if(el.getTag()=='select'){toggleCheckboxesStatus(false,namespace);el.selectedIndex=0}toggleCheckboxesStatus(false,namespace)}function listMouseEvent(el,event,normal){var sHilight='plist_shilight';var hilight='plist_hilight';if(event=='over'&&el.className!=sHilight){el.className=hilight}else if(event=='out'&&el.className!=sHilight){el.className=normal}else if(event=='click'){if(el.className==sHilight){el.className=normal}else{el.className=sHilight}}}