<?php

namespace hypeJunction\Fields;

use ElggEntity;

class HtmlField extends MetaField {

	/**
	 * {@inheritdoc}
	 */
	public function export(ElggEntity $entity) {
		$value = parent::export($entity);

		return elgg_format_html($value);
	}

}