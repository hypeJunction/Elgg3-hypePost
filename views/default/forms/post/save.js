define(function(require) {

	var elgg = require('elgg');
	var Ajax = require('elgg/Ajax');
	var $ = require('jquery');
	var lightbox = require('elgg/lightbox');
	var Form = require('ajax/Form');

	require('forms/validation');

	var $el = $('.elgg-form-post-save');
	var form = new Form($el);

	form.onSubmit(function (resolve, reject) {
		$el.parsley()
			.on('form:success', resolve)
			.on('form:error', reject)
			.validate();
	});

	form.onSuccess(function(data, statusText, xhr) {
		if ($el.closest('#colorbox').length) {
			lightbox.close();
			$('.elgg-list').trigger('refresh');
		} else {
			$('body').trigger('click'); // hide all popups and lightboxes
			this.ajax.forward(xhr.AjaxData.forward_url || data.forward_url || elgg.normalize_url(''));
		}
	});
});