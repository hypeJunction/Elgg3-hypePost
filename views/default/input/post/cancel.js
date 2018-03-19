define(function(require) {

	var elgg = require('elgg');
	var $ = require('jquery');
	var lightbox = require('elgg/lightbox');

	$(document).on('click', '.post-button-cancel', function(e) {
		e.preventDefault();

		if ($(this).closest('#colorbox').length) {
			lightbox.close();
		} else {
			elgg.forward($(this).data('href'));
		}
	});
});