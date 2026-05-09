import elgg from 'elgg';
import Ajax from 'elgg/Ajax';
import $ from 'jquery';
import lightbox from 'elgg/lightbox';
import Form from 'ajax/Form';
import 'forms/validation';

const $el = $('.elgg-form-post-save');
const form = new Form($el);

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
