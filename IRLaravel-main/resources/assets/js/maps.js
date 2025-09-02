function Maps() {
}

Maps.fn = {
    init: function () {
        if ($('.use-maps').length) {
            // Maps.fn.initMap.call(this);
            Maps.fn.placeAutoComplete.call(this);
            Maps.fn.handleSearchButton(this);
        }
    },
    initMap: function () {
        var root = this;
        var map;
        var marker = false;

        root.map = map;
        root.marker = marker;

        $(document).on('click', '.maps-marker', function (e) {
            if ($(this).hasClass('event-default')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            var _this = $(this);
            var maps = _this.closest('.maps');
            var latInput = maps.find('.latitude');
            var longInput = maps.find('.longitude');
            var mapModal = _this.closest('.maps').find('.signin-frm').attr('id'); //'modal-box-map';
            var mapView = mapModal + '-view';
            var lat = 50.162522;
            var long = 3.1806286;

            if (latInput.length && longInput.length && latInput.val() != '' && longInput.val() != '') {
                lat = latInput.val();
                long = longInput.val();
            }

            var latlng = new google.maps.LatLng(lat, long);
            var options = {zoom: 15, center: latlng};
            root.marker = new google.maps.Marker({position: latlng});

            root.map = new google.maps.Map(document.getElementById(mapView), options);
            root.marker.setMap(root.map);

            //Listen for any clicks on the map.
            google.maps.event.addListener(root.map, 'click', function (event) {
                //Get the location that the user clicked.
                var clickedLocation = event.latLng;
                //If the marker hasn't been added.
                if (root.marker === false) {
                    //Create the marker.
                    root.marker = new google.maps.Marker({
                        position: clickedLocation,
                        map: root.map,
                        draggable: true //make it draggable
                    });
                    //Listen for drag events!
                    google.maps.event.addListener(root.marker, 'dragend', function (event) {
                        markerLocation();
                    });
                } else {
                    //Marker has already been added, so just change its location.
                    root.marker.setPosition(clickedLocation);
                }
                //Get the marker's location.
                markerLocation();
            });

            //This function will get the marker's current location and then add the lat/long
            //values to our textfields so that we can save the location.
            function markerLocation() {
                //Get location.
                var currentLocation = root.marker.getPosition();
                //Add lat and lng values to a field that we can save.
                latInput.val(currentLocation.lat()).trigger('change'); //latitude
                longInput.val(currentLocation.lng()).trigger('change'); //longitude
            }

            $('#' + mapModal).modal('show');
        });
    },
    placeAutoComplete: function () {
        var bindSearch = function (element) {
            var _this = $(element);
            var _maps = _this.closest('.use-maps');
            _maps.find('.latitude').val(0);
            _maps.find('.longitude').val(0);

            var displaySuggestions = function (predictions, status) {
                if (status != google.maps.places.PlacesServiceStatus.OK || !predictions) {
                    console.log('status', status);
                    return;
                }

                _maps.find(".place-results").html('').show();
                predictions.forEach((prediction) => {
                    let _class = '';
                    let place_id = '';
                    var html = '<li>';

                    if (typeof prediction.types != 'undefined' && (
                        prediction.types.includes("street_address") ||
                        prediction.types.includes("premise") ||
                        prediction.types.includes("subpremise")
                    )) {
                        _class = 'select-address';
                        place_id = prediction.place_id;
                    }

                    html += '<div class="address-text ' + _class + '" data-place_id="' + place_id + '">' + prediction.description + '</div>';
                    html += '<div class="wrap-address">';

                    if (typeof prediction.types != 'undefined'
                        && !prediction.types.includes("street_address")
                        && !prediction.types.includes("premise")
                        && !prediction.types.includes("subpremise")) {
                        //Check address type and show input number
                        html += '<span class="address-plus"> + </span>';
                        html += '<input class="address-street-number" type="text" placeholder="' + Lang.get('workspace.address_number') + '" data-address="' + prediction.description + '">';
                    }
                    
                    html += '</div>';
                    html += '</li>';
                    _maps.find(".place-results").append(html);
                });
            };

            let keyword = _this.val();
            if (typeof _this.data('address') != 'undefined') {
                keyword = _this.val() + " " + _this.data('address');
            }
            var service = new google.maps.places.AutocompleteService();
            const request = {
                input: keyword,
                // fields: ['formatted_address,geometry,place_id,description']
            };
            // service.getQueryPredictions(request, displaySuggestions);
            service.getPlacePredictions(request, displaySuggestions);
        };

        $('.maps .location').map(function () {
            if ($(this).val().length) {
                // bindSearch(this);
            }
        });
        
        $('.maps .location').on('keyup keypress', function(e) {
            if ($('.place-results').is(':visible') || ($('.place-results li').length) < 1) {
                return e.which !== 13;
            }
        });

        $(document).on('click', '.place-results .address-text, .place-results li', function (event) {
            $(this).closest('li').find('.address-street-number').focus();
        });

        $(document).on('keyup', '.maps .location', Maps.fn.delay(function (event) {
            if (event.which == '13') {
                event.preventDefault();
            }

            if ($(this).val().length) {
                bindSearch(this);
            }
        }, 1000));

        $(document).on('keyup', '.address-street-number', Maps.fn.delay(function (event) {
            if ($(this).val().length) {
                bindSearch(this);
            }
        }, 1000));

        $(document).on('change', '.address-street-number', function (event) {
            if ($(this).val().length) {
                bindSearch(this);
            }
        });

        $(document).on('click', '.select-address', function () {
            var _maps = $(this).closest('.use-maps');
            var place_id = $(this).data('place_id');
            var latInput = _maps.find('.latitude');
            var longInput = _maps.find('.longitude');

            const map = new google.maps.Map(document.getElementById("modal-box-map-view"), {
                center: {lat: -33.866, lng: 151.196},
                zoom: 15,
            });
            const request = {
                placeId: place_id,
                fields: ['formatted_address,geometry']
            };
            const service = new google.maps.places.PlacesService(map);

            service.getDetails(request, (place, status) => {
                if (
                    status === google.maps.places.PlacesServiceStatus.OK &&
                    place &&
                    place.geometry &&
                    place.geometry.location
                ) {
                    if (place.geometry.location.lat() && place.geometry.location.lng()) {
                        latInput.val(place.geometry.location.lat()).trigger('change');
                        longInput.val(place.geometry.location.lng()).trigger('change');

                        _maps.find(".location").val(place.formatted_address);
                    }
                }
            });

            let locationSearch = $(this).closest('.location-search');
            if (locationSearch.length > 0) {
                let btnSearch = locationSearch.find('a.btn-search');
                btnSearch.addClass('active');
                btnSearch.removeAttr('disabled');
            }

            _maps.find(".place-results").hide();

            // $("#form-register .btn-modal-register, #form-register .btn-register").prop('disabled', false);
            // Enable btn after select address
            $(".btn-order, .btn-pr-custom").removeClass('disableBtn').prop('disabled', false);
            if ($('.use-maps').find('.error').length != 0) {
                $('.use-maps').find('.error').remove();
            }
        });
    },

    handleSearchButton: function () {
        $(document).on('change', 'input.location-search.location', function () {
            if ($(this).val() == '') {
                let locationSearch = $(this).closest('.container.location-search');
                if (locationSearch.length > 0) {
                    let btnSearch = locationSearch.find('a.btn-search');
                    btnSearch.removeClass('active');
                    btnSearch.attr('disabled', true);
                }
            }
        });

        $(document).on('click', '.location-search .btn.btn-search', function () {
            if (typeof $(this).attr('disabled') === 'undefined') {
                $('.location-search form').submit();
            }
        });
    },

    delay: function (callback, ms) {
        var timer = 0;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    },

    rule: function () {
        $(document).ready(function () {
            Maps.fn.init.call(this);
        });
    }
};

Maps.fn.rule();
