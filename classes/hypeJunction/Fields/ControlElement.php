<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

class ControlElement extends Field {

	/**
	 * {@inheritdoc}
	 */
	public function raw(Request $request, ElggEntity $entity) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value) {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function retrieve(ElggEntity $entity) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function label(ElggEntity $entity) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function help(ElggEntity $entity) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function placeholder(ElggEntity $entity) {
		return null;
	}
}