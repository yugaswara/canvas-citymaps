(function ($) {

 	$('.cmb-type-pw_map').each(function() {
		var searchInput = $('.map-search', this).get(0);
		var mapCanvas = $('.map', this).get(0);
		var latitude = $('.latitude', this);
		var longitude = $('.longitude', this);
		var latLng = new google.maps.LatLng(-6.914744, 107.609811);
		var zoom = 12;

		// Map
		if(latitude.val().length > 0 && longitude.val().length > 0) {
			latLng = new google.maps.LatLng(latitude.val(), longitude.val());
			zoom = 12;
		}

		var mapOptions = {
			center: latLng,
			zoom: zoom,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		var map = new google.maps.Map(mapCanvas, mapOptions);

		// Marker
		var markerOptions = {
			map: map,
			position: latLng,
			draggable: true,
			title: 'Drag to set the exact location'
		};
		var marker = new google.maps.Marker(markerOptions);

		if(latitude.val().length > 0 && longitude.val().length > 0) {
			marker.setPosition(latLng);
		}

		google.maps.event.addListener(marker, 'drag', function() {
			latitude.val(marker.getPosition().lat());
			longitude.val(marker.getPosition().lng());
		});

		// Search
		var autocomplete = new google.maps.places.Autocomplete(searchInput);
		autocomplete.bindTo('bounds', map);

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			} else {
				map.setCenter(place.geometry.location);
				map.setZoom(17);
			}

			marker.setPosition(place.geometry.location);

			latitude.val(place.geometry.location.lat());
			longitude.val(place.geometry.location.lng());
		});

		$(searchInput).keypress(function(e) {
			if(e.keyCode == 13) {
				e.preventDefault();
			}
		});
    });

}(jQuery));