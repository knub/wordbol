jQuery(function($) {
	$("#wordpress-stanbol-entities > label > div").each(function(i, el) {
		$(el).knubtip("init", {
			'wait-time': 400,
			'info-class': '.wordpress-stanbol-entities-info'
		});
	});

	initialize();
	var placesCount = places.length;
	placesLocations = [];
	places.forEach(function(address) {
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				console.log(results[0]);
				placesLocations.push({
					'place': results[0].geometry.location,
					'bounds': results[0].geometry.viewport
				});
				if (placesLocations.length === placesCount) {
					configureMapWithPlaces(placesLocations);
				}
			} else
				alert('Geocode was not successful for the following reason: ' + status);
		});
	});
	if (places.length > 0)
		addToMap(places[0]);
});

var geocoder;
var map;
function initialize() {
	geocoder = new google.maps.Geocoder();
	var mapOptions = {
		zoom: 2
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

function addToMap(address) {
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}
function configureMapWithPlaces(placesLocations) {
	var bounds = new google.maps.LatLngBounds();
	placesLocations.forEach(function(geometry) {
		var marker = new google.maps.Marker({
			map: map,
			position: geometry.place
		});
		// Code to center around the markers from here:
		// http://blog.shamess.info/2009/09/29/zoom-to-fit-all-markers-on-google-maps-api-v3/
		bounds.extend(geometry.bounds.getNorthEast());
		bounds.extend(geometry.bounds.getSouthWest());
	});
	map.fitBounds (bounds);
}
