var initLocations = function(prefix) {
    $(prefix+'country').addEvent('change', function (e) {
        e.stop();
        getProvincesUrl = getProvincesBaseUrl + $(prefix+'country').getProperty('value');
        getCitiesUrl    = getCitiesBaseUrl + $(prefix+'province').getProperty('value')  + '/' + $(prefix+'country').getProperty('value');

        fetchData(getProvincesUrl, prefix+'province');
        fetchData(getCitiesUrl, prefix+'city');
    });

    $(prefix+'province').addEvent('change', function (e) {
        e.stop();
        getCitiesUrl = getCitiesBaseUrl + $(prefix+'province').getProperty('value')  + '/' + $(prefix+'country').getProperty('value');
        fetchData(getCitiesUrl, prefix+'city');
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
