jQuery(function($) {
	$("#wordpress-stanbol-entities > label > div").each(function(i, el) {
		$(el).knubtip("init", {
			'wait-time': 400,
			'info-class': '.wordpress-stanbol-entities-info'
		});
	});

	runStanbol($);
});

function runStanbol($) {
	window.setTimeout(function() {
		$.ajax({
			url: ajaxurl,
			data: {
				post_id: POST_ID,
				action: "run_stanbol"
			},
			success: function (data) {
				$("#stanbol_content").html(data);
				maps.initialize();
				maps.geocode(places);
			}
		});
	}, 100);
}
