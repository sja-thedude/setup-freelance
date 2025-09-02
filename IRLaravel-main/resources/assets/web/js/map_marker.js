function MapMarker() {}

MapMarker.fn = {
    init: function (){
        MapMarker.fn.handleShowMap.call(this);
    },
    initMap: function (markers) {
        if($('#map').length) {
            let map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: new google.maps.LatLng(parseFloat($('.latitude').val()), parseFloat($('.longitude').val())),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            let infowindow = new google.maps.InfoWindow();
            let marker, i;

            $.each(markers, function (workspaceId, markerItem) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(markerItem.latitude, markerItem.longtitude),
                    map: map,
                    icon: $('.show-map').data('markericon')
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(markerItem.markerHtml);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            })

            $('#map').show()
            $('.main-search-content').hide()
            $('.back-button-maps').show()
        }
    },
    handleShowMap: function () {
        $('.show-map').on('click', function () {
            let workspaceIdList = $('#workspaceIdList').val()
            let mapUrl = $(this).data('url')
            $.ajax({
                type: 'POST',
                url: mapUrl,
                dataType: 'json',
                data: {'workspaceIdList': workspaceIdList},
                success: function (response) {
                    if (response.success) {
                        let markers = response.data.markers
                        MapMarker.fn.initMap.call(this, markers)
                    }
                }
            })
        })

        $('.back-button-maps').on('click', 'a.back-button', function () {
            $('#map').hide()
            $('.main-search-content').show()
            $('.back-button-maps').hide()
        })
    },
    rule: function (){
        $(document).ready(function () {
            MapMarker.fn.init.call(this);
        })
    }
};

MapMarker.fn.rule();