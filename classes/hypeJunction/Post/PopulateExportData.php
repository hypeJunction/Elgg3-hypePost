<?php

namespace hypeJunction\Post;

use Elgg\Event;
use hypeJunction\Fields\Field;

/**
 * PopulateExportData class.
 */
class PopulateExportData {

	/**
	 * Populate export fields
	 *
	 * @param Hook $hook Hook
	 * @return array
	 */
	public function __invoke(Event $event) {

		$value = $event->getValue();
		$entity = $event->getEntityParam();

		if (!$entity) {
			return;
		}

		$fields = Model::instance()->getFields($entity, Field::CONTEXT_EXPORT);

		foreach ($fields as $field) {
			$value[$field->name] = $field->retrieve($entity);
		}

		return $value;
	}
}
