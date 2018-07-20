<?php

namespace hypeJunction\Countries;

use ElggEntity;
use hypeJunction\Fields\Field;
use hypeJunction\Fields\MetaField;
use Symfony\Component\HttpFoundation\ParameterBag;

class AddressField extends MetaField {

	/**
	 * {@inheritdoc}
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		$address_parts = $parameters->get($this->name);

		if (!is_array($address_parts) || empty(array_filter($address_parts))) {
			return;
		}

		$address = new Address();

		foreach ($address_parts as $key => $value) {
			$address->$key = $value;
		}

		$md_name = $this->name;
		$entity->$md_name = serialize($address);
	}

	/**
	 * {@inheritdoc}
	 */
	public function retrieve(ElggEntity $entity) {
		$md_name = $this->name;
		$value = $entity->$md_name;

		if (!$value) {
			return null;
		}

		return unserialize($value);
	}
}