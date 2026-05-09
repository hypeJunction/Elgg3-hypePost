import elgg from 'elgg';
import $ from 'jquery';
import lightbox from 'elgg/lightbox';

$(document).on('click', '.post-button-cancel', function(e) {
	e.preventDefault();

	if ($(this).closest('#colorbox').length) {
		lightbox.close();
	} else {
		elgg.forward($(this).data('href'));
	}
});
