<?php

namespace hypeJunction\Post;

use Elgg\Hook;
use hypeJunction\Fields\Field;

class PopulateExportData {

	/**
	 * Populate export fields
	 *
	 * @param Hook $hook Hook
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$value = $hook->getValue();
		$entity = $hook->getEntityParam();

		$fields = Model::instance()->getFields($entity, Field::CONTEXT_EXPORT);

		foreach ($fields as $field) {
			$value[$field->name] = $field->retrieve($entity);
		}

		return $value;
	}
}