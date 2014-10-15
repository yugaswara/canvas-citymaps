
function initialize() {
	var mapOptions = {
		zoom: 12,
		center: new google.maps.LatLng(-6.914744, 107.609811),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		styles: [
			{stylers: [{ visibility: 'on' }]},
			{elementType: 'labels', stylers: [{ visibility: 'on' }]}
		]
	}
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	var marker1 = new Marker({
		map: map,
		zIndex: 9,
		title: 'Map Icons',
		position:  new google.maps.LatLng(-6.914744, 107.609811),
		icon: {
			path: SQUARE_PIN,
			fillColor: '#0E77E9',
			fillOpacity: 1,
			strokeColor: '',
			strokeWeight: 0,
			scale: 1/2
		},
		label: '<i class="map-icon-walking"></i>'
	});
	
	var infoWindow = new google.maps.InfoWindow( {
		content: "<h3>Emerald Tower</h3><p>Jl. Kawaluyaan</p>"
	} );

	google.maps.event.addListener( marker1, "click", function()
	{
		infoWindow.open( map, marker1 );
	} );

	var marker2 = new Marker({
		map: map,
		zIndex: 9,
		title: 'Map Icons',
		position:  new google.maps.LatLng(-6.854744, 107.609811),
		icon: {
			path: SQUARE_PIN,
			fillColor: '#0E77E9',
			fillOpacity: 1,
			strokeColor: '',
			strokeWeight: 0,
			scale: 1/2
		},
		label: '<i class="map-icon-cafe"></i>'
	});
	
	var marker3 = new Marker({
		map: map,
		zIndex: 9,
		title: 'Map Icons',
		position:  new google.maps.LatLng(-6.914744, 107.559811),
		icon: {
			path: SQUARE_PIN,
			fillColor: '#0E77E9',
			fillOpacity: 1,
			strokeColor: '',
			strokeWeight: 0,
			scale: 1/2
		},
		label: '<i class="map-icon-car-wash"></i>'
	});
	
	var marker4 = new Marker({
		map: map,
		zIndex: 9,
		title: 'Map Icons',
		position:  new google.maps.LatLng(-6.894744, 107.709811),
		icon: {
			path: SQUARE_PIN,
			fillColor: '#0E77E9',
			fillOpacity: 1,
			strokeColor: '',
			strokeWeight: 0,
			scale: 1/2
		},
		label: '<i class="map-icon-male"></i>'
	});
}

google.maps.event.addDomListener(window, 'load', initialize);
