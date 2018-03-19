define(function(require) {

	var $ = require('jquery');

	$(document).on('click', '.elgg-module-collapse > .elgg-head', function(e) {
		if ($(e.target).is('a')) {
			e.preventDefault();
		}

		$(this).closest('.elgg-module-collapse')
			.toggleClass('elgg-state-collapsed elgg-state-expanded');
	});

});
