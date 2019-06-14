<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;

class BooleanField extends Field {

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
		$val = $request->getParam($this->name);

		if ($val !== null) {
			return (bool) $val;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function normalize(ElggEntity $entity) {
		$props =  parent::normalize($entity);

		if ($this->type === 'checkbox') {
			$props['checked'] = (bool) $props['value'];
			$props['value'] = 1;
			$props['default'] = 0;
		}

		return $props;
	}
}