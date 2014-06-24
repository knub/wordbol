jQuery(function($) {

	var placesCount = 0;
	allPlaces.forEach(function(places) {
		console.log(places);
		var map = maps.initialize(document.getElementById("map-canvas" + placesCount));
		if (places.length > 0)
			maps.configureMapWithPlaces(places, map);
		else
			$("#map-canvas" + placesCount).css("display", "none");
		placesCount += 1;
	});
});
