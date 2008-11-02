var changeCities = function(cities) {
    var citiesDiv = $('city').getParent();
    var select = '<select name="city" style="width:180px" id="city">';

    cities.each(function(city) {
        select += '<option value="' + city.code + '">' + city.city + '</option>';
    });

    select += '</select>';

    citiesDiv.set('html', select);
}
