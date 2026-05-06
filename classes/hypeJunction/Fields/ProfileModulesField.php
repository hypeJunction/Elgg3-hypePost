<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;
use hypeJunction\Post\Post;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * ProfileModulesField class.
 */
class ProfileModulesField extends MetaField {

	/**
	 * {@inheritdoc}
	 */
	public function retrieve(ElggEntity $entity) {
		$modules = Post::instance()->getModules($entity);

		$data = [];

		foreach ($modules as $name => $module) {
			$data[$name] = Post::instance()->usesModule($entity, $name);
		}

		return $data;
	}

	/**
	 * raw.
	 *
	 * @param Request    $request request
	 * @param ElggEntity $entity  entity
	 *
	 * @return mixed
	 */
	public function raw(Request $request, ElggEntity $entity) {
		return parent::raw($request, $entity) ?: [];
	}

	/**
	 * save.
	 *
	 * @param ElggEntity   $entity     entity
	 * @param ParameterBag $parameters parameters
	 *
	 * @return mixed
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		$name = $this->name;
		$value = $parameters->get($name);

		$modules = Post::instance()->getModules($entity);

		foreach ($modules as $name => $uses) {
			$entity->{"uses_module:$name"} = in_array($name, $value);
		}
	}
}
