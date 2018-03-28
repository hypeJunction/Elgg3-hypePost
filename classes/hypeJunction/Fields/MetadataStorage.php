<?php

namespace hypeJunction\Fields;

use ElggEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

trait MetadataStorage {

	/**
	 * Store raw value as an entity property
	 *
	 * @param ElggEntity   $entity Entity
	 * @param ParameterBag $parameters Raw data
	 *
	 * @return bool
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		$name = $this->name;
		$value = $parameters->get($name);
		$entity->$name = $value;
	}

	/**
	 * Retrieve entity property
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return mixed
	 */
	public function retrieve(ElggEntity $entity) {
		$name = $this->name;
		return $entity->$name;
	}

}