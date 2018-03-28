define(function(require) {

	var elgg = require('elgg');
	var Ajax = require('elgg/Ajax');
	var $ = require('jquery');
	var lightbox = require('elgg/lightbox');
	var spinner = require('elgg/spinner');
	require('forms/validation');

	$('.elgg-form-post-save').parsley();

	$(document).on('submit', '.elgg-form-post-save', function(e) {

		e.preventDefault();

		var $form = $(this);

		var ajax = new Ajax();

		ajax.action($form.attr('action'), {
			data: ajax.objectify($form),
			beforeSend: function() {
				$form.find('[type="submit"]').prop('disabled', true);
			}
		}).done(function(data) {
			if ($form.closest('#colorbox').length) {
				lightbox.close();
				$('.elgg-list').trigger('refresh');
			} else {
				ajax.forward(data.forward_url || data.entity.url || elgg.normalize_url(''));
			}
		}).fail(function() {
			$form.find('[type="submit"]').prop('disabled', false);
		});
	});
});