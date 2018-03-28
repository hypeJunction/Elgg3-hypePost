<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;

class TitleField extends Field {

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
		return elgg_get_title_input($this->name);
	}

}