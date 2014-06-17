jQuery(function($) {
	maps.initialize();
	if (places.length > 0)
		maps.configureMapWithPlaces(places);
	else
		$("#map-canvas").css("display", "none");
});
