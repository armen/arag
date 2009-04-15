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
        getCitiesUrl = getCitiesBaseUrl + $(province_name).getProperty('value')  + '/' + $(country_name).getProperty('value');
        fetchData(getCitiesUrl, city_name);
    });
}

var fetchData = function(sourceUrl, destination) {

    var request = new Request.JSON({
        url: sourceUrl,
        onComplete: function(jsonObj) {
            updateSelect(jsonObj.entries, destination);
        }
    }).send();
}

var updateSelect = function(options, name) {

    var container = $(name+'_container');

    if (options.length) {
        var select = $(name), optionTags;

        options.each(function(option) {
            optionTags += '<option value="' + option.key + '">' + option.value + '</option>';
        });
        select.set('html', optionTags);
        container.appendChild(select);

        new Fx.Slide(container).slideIn();

    } else {
        $(name).setProperty('value', 0);
        new Fx.Slide(container).slideOut();
    }
}
