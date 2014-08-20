jQuery(function($) {
	runStanbol($);
});

function init($) {
	var map = maps.initialize(document.getElementById('map-canvas'));
	maps.geocode(places, map);
	$("#wordbol-entities > label > div").each(function(i, el) {
		$(el).knubtip("init", {
			'wait-time': 1000,
			'info-class': '.wordbol-entities-info'
		});
	});
	$(".place_location").click(function(e) {
		var $el = $(e.currentTarget);
		// we have to invert the checked status because the click is executed before the change
		var checked = !$el.parent().prev().prop('checked');
		var resource = $el.data("location");
		placesLocations.forEach(function(el) {
			if (el.resource === resource)
				el.selected = checked;
		});
		maps.configureMapWithPlaces(placesLocations, map);
	});
}

function runStanbol($) {
	if (typeof(POST_ID) === "undefined")
		return;
	window.setTimeout(function() {
		$.ajax({
			url: ajaxurl,
			data: {
				post_id: POST_ID,
				action: "run_stanbol"
			},
			success: function (data) {
				$("#stanbol_content").html(data);
				init($);
			}
		});
	}, 100);
}
