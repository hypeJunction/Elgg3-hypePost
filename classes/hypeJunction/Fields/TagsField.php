<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;

class TagsField extends Field {

	use MetadataStorage;

	/**
	 * Prepare raw value from request data and sanitize/normalize it
	 * Raw data will be assembled into a ParameterBag and passed on to storage methods
	 *
	 * @param Request    $request Request object
	 * @param ElggEntity $entity  Entity
	 *
	 * @return mixed
	 */
	public function raw(Request $request, ElggEntity $entity) {
		$value = $request->getParam($this->name);

		if (is_string($value)) {
			$value = string_to_tag_array($value);
		}

		return $value;
	}

}