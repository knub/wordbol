jQuery(function($) {
	$("#wordpress-stanbol-entities > label > div").each(function(i, el) {
		$(el).knubtip("init", {
			'wait-time': 100,
			'info-class': '.wordpress-stanbol-entities-info'
		});
	});
});