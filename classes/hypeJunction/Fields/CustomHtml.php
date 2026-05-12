<?php

namespace hypeJunction\Fields;

use ElggEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

class CustomHtml extends Field {

	/**
     * @param ElggEntity $entity
     * @param ParameterBag $parameters
     * @return mixed
     */
    public function save(ElggEntity $entity, ParameterBag $parameters) {
		return null;
	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
    public function retrieve(ElggEntity $entity) {
		return null;
	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
    public function label(ElggEntity $entity) {
		return null;
	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
    public function help(ElggEntity $entity) {
		return null;
	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
    public function placeholder(ElggEntity $entity) {
		return null;
	}

	/**
     * @param ElggEntity $entity
     * @param mixed $context
     * @return mixed
     */
    public function render(\ElggEntity $entity, $context = null) {
		$html = $this->{'#html'};
		if ($html instanceof \Closure) {
			$html = $html($entity, $this);
		}

		if ($html) {
			return elgg_format_element('div', [
				'class' => 'elgg-field elgg-col elgg-col-1of1',
			], $html);
		}

		return '';
	}
}