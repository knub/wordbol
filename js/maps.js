(function($) {
	maps = {};
	maps.initialize = function() {
		maps.geocoder = new google.maps.Geocoder();
		var mapOptions = {
			zoom: 2
		};
		maps.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	};

	maps.geocode = function(places) {
		var placesNumber = places.length;
		var placesCount = 0;
		placesLocations = [];
		places.forEach(function(place) {
			maps.geocoder.geocode( { 'address': place.address}, function(results, status) {
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
					maps.configureMapWithPlaces(placesLocations);
				}
			});
		});
	};

	maps.configureMapWithPlaces = function(placesLocations) {
		var bounds = new google.maps.LatLngBounds();
		placesLocations.forEach(function(geometry) {
			var infowindow = new google.maps.InfoWindow({
				content: "<strong>" + geometry.text + "</strong>"
			});
			var marker = new google.maps.Marker({
				map: maps.map,
				position: geometry.place,
				title: geometry.text
			});
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(maps.map, marker);
			});
			// Code to center around the markers from here:
			// http://blog.shamess.info/2009/09/29/zoom-to-fit-all-markers-on-google-maps-api-v3/
			bounds.extend(geometry.bounds.getNorthEast());
			bounds.extend(geometry.bounds.getSouthWest());
		});
		maps.map.fitBounds (bounds);
	}

}(jQuery));
