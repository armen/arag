var initLocations = function(country_name, province_name, city_name) {
    $(country_name).addEvent('change', function (e) {
        e.stop();
        getProvincesUrl = getProvincesBaseUrl + ($(country_name).getProperty('value') ? $(country_name).getProperty('value') : 0);
        getCitiesUrl    = getCitiesBaseUrl + ($(province_name).getProperty('value') ? $(province_name).getProperty('value') : 0)   + '/' + ($(country_name).getProperty('value') ? $(country_name).getProperty('value') : 0);

        fetchData(getProvincesUrl, province_name);
        fetchData(getCitiesUrl, city_name);
    });

    $(province_name).addEvent('change', function (e) {
        e.stop();
        getCitiesUrl = getCitiesBaseUrl + ($(province_name).getProperty('value') ? $(province_name).getProperty('value') : 0) + '/' + $(country_name).getProperty('value');
        fetchData(getCitiesUrl, city_name);
    });
}

var fetchData = function(sourceUrl, destination, selected) {

    var request = new Request.JSON({
        url: sourceUrl,
        onComplete: function(jsonObj) {
            updateSelect(jsonObj.entries, destination, selected);
        }
    }).send();
}

var updateSelect = function(options, name, selected) {
    var container = $(name+'_container');
    if (options.length) {
        var select = $(name);
        var isSelected = false;
        select.empty();
        new Element('option', {'value':'', 'html':''}).inject(select);
        options.each(function(option) {
            isSelected = (option.key == selected) ? true : false;
            new Element('option', {'value':option.key, 'html':option.value, 'selected' : isSelected}).inject(select);
        });
        container.appendChild(select);
        new Fx.Slide(container).slideIn();
    } else {
        var select = $(name);
        var isSelected = false;
        select.empty();
        container.appendChild(select);
        new Fx.Slide(container).slideOut();
    }
}
