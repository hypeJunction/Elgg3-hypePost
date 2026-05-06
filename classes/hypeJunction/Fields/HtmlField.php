<?php

namespace hypeJunction\Fields;

use ElggEntity;

/**
 * HtmlField class.
 */
class HtmlField extends MetaField {

	/**
	 * {@inheritdoc}
	 */
	public function export(ElggEntity $entity) {
		$value = parent::export($entity);

		return elgg_format_html($value);
	}
}
