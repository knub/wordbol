jQuery(function($) {
	$("#wordpress-stanbol-entities > label > div").each(function(i, el) {
		$(el).knubtip("init", {
			'wait-time': 400,
			'info-class': '.wordpress-stanbol-entities-info'
		});
	});

	initialize();
	var placesNumber = places.length;
	var placesCount = 0;
	placesLocations = [];
	places.forEach(function(place) {
		geocoder.geocode( { 'address': place.address}, function(results, status) {
			placesCount += 1;
			if (status == google.maps.GeocoderStatus.OK) {
				var location = 	{
					place: results[0].geometry.location,
					bounds: results[0].geometry.viewport,
					text: place.address
				};
				$("#" + place.id).attr("value", JSON.stringify(location));
				placesLocations.push(location);
				console.log("Geocode successful.");
			} else
				console.error('Geocode was not successful for the following reason: ' + status);

			if (placesCount === placesCount) {
				configureMapWithPlaces(placesLocations);
			}
		});
	});
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

function configureMapWithPlaces(placesLocations) {
	var bounds = new google.maps.LatLngBounds();
	placesLocations.forEach(function(geometry) {
		var infowindow = new google.maps.InfoWindow({
			content: "<strong>" + geometry.text + "</strong>"
		});
		var marker = new google.maps.Marker({
			map: map,
			position: geometry.place,
			title: geometry.text
		});
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map, marker);
		});
		// Code to center around the markers from here:
		// http://blog.shamess.info/2009/09/29/zoom-to-fit-all-markers-on-google-maps-api-v3/
		bounds.extend(geometry.bounds.getNorthEast());
		bounds.extend(geometry.bounds.getSouthWest());
	});
	map.fitBounds (bounds);
}
