Arag_Map = new Class({
        Implements   : [Events,Options],
        options      : {
            id           : false,
            key          : false,
            minX         : false,
            maxX         : false,
            minY         : false,
            maxY         : false,
            weather      : true,
            photos       : true,
            path         : true
        },
        map          : false,

        initialize   : function(options) {
            this.setOptions(options);

            Asset.javascript('http://www.google.com/jsapi?sensor=false&key='+this.options.key,{
                onload:this.loadMaps.bind(this)
            });

            if (typeof(google) != 'undefined') { //Onload gets called if its not loaded from cache.
                this.loadMaps();
            }
        },

        loadMaps : function() {
            if (typeof(this.isLoaded == 'undefined')) { //Just to make sure we load it once and we will call show() just once
                this.isLoaded = true;
                google.load("maps", "2.147", {"callback" : this.show.bind(this)});
            }
        },

        show : function() {
            this.map          = new GMap2($(this.options.id));

            var tourSW        = new GLatLng( this.options.minX, this.options.minY );
            var tourNE        = new GLatLng( this.options.maxX, this.options.maxY );
            var tourArea      = new GLatLngBounds( tourSW, tourNE );

            this.map.setCenter(tourArea.getCenter(), this.map.getBoundsZoomLevel(tourArea));

            this.map.setUIToDefault();

            this.destinations = new Hash();

            this.layers       = new Hash();
            this.layers.set('destinations', new MarkerManager(this.map));
            this.layers.set('path', new MarkerManager(this.map));
            this.layers.set('photos', new MarkerManager(this.map));
            this.layers.set('weather', new MarkerManager(this.map));

            this.fireEvent('onload');

            if (this.options.path) {
                this.showPath();
            }
            if (this.options.photos) {
                this.getPanoramioPhotos();
            }
            if (this.options.weather) {
                this.getWeatherIcons();
            }
        },

        addDestination : function(name, latitude, longitude) {
            var destination = new Hash({'name':name, 'latitude':latitude, 'longitude':longitude});
            this.destinations.set(name, destination);

            var i = new GIcon(G_DEFAULT_ICON);
            var t = 'Click for more info';
            var ll = new GLatLng(latitude, longitude);
            var marker = new GMarker(ll, {icon:i, title:t});
            this.layers.get('destinations').addMarker(marker, 0);
        },

        showPath : function() {
            var lls = Array();

            getPath = function(dest, name) {
                this.include(new GLatLng(dest.get('latitude'), dest.get('longitude')));
            }.bind(lls);

            this.destinations.each(getPath);

            this.layers.set('path', new GPolyline(lls, "#ff0000"));
            this.map.addOverlay(this.layers.get('path'));
        },

        getPanoramioPhotos : function() {
            var get = function(dest) {
                var add = function(response) {
                    if (!response) {
                        return false;
                    }
                    dest.set('photos', response.photos);
                    this.showPhotos(dest.get('photos'));
                }

                var Req = new Request.JSON({
                                method: 'get',
                                url: this.options.panoramio_url+'/search/'+dest.get('longitude')+'/'+dest.get('latitude'),
                                onSuccess: add.bind(this)
                        });
                        Req.send();
            }
            this.destinations.each(get.bind(this));

            var rezoom = function(old_level, new_level) {
                this.layers.get('photos').clearMarkers();    

                this.destinations.each(function(dest) {
                    this.showPhotos(dest.get('photos'));
                }.bind(this));
            }

            GEvent.addListener(this.map, 'zoomend', rezoom.bind(this));
        },

        getWeatherIcons : function() {
            var get = function(dest) {
                var add = function(weather) {
                    if (!weather) {
                        return false;
                    }
                    var i        = new GIcon(G_DEFAULT_ICON, this.options.weather_icons+'/61x61/'+weather.conditionIcon+'.png');
                    i.iconSize   = new GSize(61, 61);
                    i.iconAnchor = new GPoint(0, 30);
                    i.imageMap   = [0,0, 0,61, 61,61, 61,0];
                    i.shadow     = '';
                    var marker   = new GMarker(new GLatLng(dest.get('latitude'), dest.get('longitude')), {icon:i});
                    this.layers.get('weather').addMarker(marker, 0);
                }

                var Req = new Request.JSON({
                                method: 'get',
                                url: this.options.weather_url+'/get/'+dest.get('name'),
                                onSuccess: add.bind(this)
                        });
                        Req.send();
            }
            this.destinations.each(get.bind(this));
        },

        showPhotos : function(photos) {
            photos.each(function(photo, index) {
                var scale = 3;

                if (index < 1) {
                    scale = 10;
                }

                var width     = photo.width*(this.map.getZoom()/100)*scale;
                var height    = photo.height*(this.map.getZoom()/100)*scale;

                this.showPhoto(photo, width, height);
            }.bind(this));
        },

        showPhoto: function (photo, width, height) {
            var icon      = new GIcon(G_DEFAULT_ICON, photo.photo_file_url);
            icon.shadow   = '';
            icon.iconSize = new GSize(width, height);
            icon.imageMap = [0,0, 0,width, height,width, height,0];
            var marker    = new GMarker(new GLatLng(photo.latitude, photo.longitude), { icon : icon });
            marker.photo  = photo;
            marker.map    = this.map;
            this.layers.get('photos').addMarker(marker, this.map.getZoom(), this.map.getZoom());

            GEvent.addListener(marker, 'click', function(LatLng) {
                var spacer    = new Element('br');
                var title     = new Element('span').set('html', photo.photo_title);
                var image     = new Element('img', {src:photo.photo_file_url, width:photo.width, height:photo.height});
                var Container = new Element('div').adopt(title, spacer, image);
                this.map.openInfoWindowHtml(this.getLatLng(), Container.get('html'));
            });
        }
});