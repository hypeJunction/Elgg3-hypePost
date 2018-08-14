define(function(require) {

	var $ = require('jquery');
	var elgg = require('elgg');
	require('jquery-ui');

	$('.elgg-input-range__js-container').each(function() {

		var $parent = $(this);
		var $slider = $parent.find('.elgg-input-range__sliders');
		var $lower = $parent.find('.elgg-input-range__lower-bound');
		var $upper = $parent.find('.elgg-input-range__upper-bound');

		var options = $slider.data('options');

		options.slide = function(event, ui) {
			$lower.val(ui.values[0]);
			$upper.val(ui.values[1]);

			var $label = $('<span>').text(ui.value);
			$(ui.handle).html($label);
		};

		$slider.slider(options);
		$slider.trigger('slide');
	});

});