(function($) {
	maps = {};
	var allMarkers = {};
	maps.initialize = function(el) {
		maps.geocoder = new google.maps.Geocoder();
		var mapOptions = { };
		allMarkers[el.id] = [];
		var map = new google.maps.Map(el, mapOptions);
		return map;
	};

	maps.geocode = function(places, map) {
		var placesNumber = places.length;
		var placesCount = 0;
		placesLocations = [];
		places.forEach(function(place) {
			maps.geocoder.geocode( { 'address': place.address}, function(results, status) {
				placesCount += 1;
				if (status == google.maps.GeocoderStatus.OK) {
					var geometry = results[0].geometry;
					var location = 	{
						place: {
							lat: geometry.location.lat(),
							lng: geometry.location.lng()
						},
						northEast: {
							lat: geometry.viewport.getNorthEast().lat(),
							lng: geometry.viewport.getNorthEast().lng()
						},
						southWest: {
							lat: geometry.viewport.getSouthWest().lat(),
							lng: geometry.viewport.getSouthWest().lng()
						},
						text: place.address,
						resource: place.resource,
						selected: place.selected
					};
					$("#" + place.id).attr("value", JSON.stringify(location));
					placesLocations.push(location);
					console.log("Geocode successful.");
				} else
					console.error('Geocode was not successful for the following reason: ' + status);

				if (placesCount === placesNumber) {
					maps.configureMapWithPlaces(placesLocations, map);
					console.log(placesLocations);
				}
			});
		});
	};

	maps.configureMapWithPlaces = function(placesLocations, map) {
		var id = map.getDiv().id;
		var markers = allMarkers[id];
		markers.forEach(function(marker) {
			marker.setMap(null);
		});
		var bounds = new google.maps.LatLngBounds();
		placesLocations.forEach(function(geometry) {
			if (!geometry.selected)
				return;
			var infoWindow = new google.maps.InfoWindow({
				content: "<strong>" + geometry.text + "</strong>"
			});
			var marker = new google.maps.Marker({
				map: map,
				position: geometry.place,
				title: geometry.text
			});
			allMarkers[id].push(marker);
			google.maps.event.addListener(marker, 'click', function () {
				infoWindow.open(map, marker);
			});
			// Code to center around the markers from here:
			// http://blog.shamess.info/2009/09/29/zoom-to-fit-all-markers-on-google-maps-api-v3/
			bounds.extend(new google.maps.LatLng(geometry.northEast.lat, geometry.northEast.lng));
			bounds.extend(new google.maps.LatLng(geometry.southWest.lat, geometry.southWest.lng));
		});
		map.fitBounds(bounds);
	}
}(jQuery));
