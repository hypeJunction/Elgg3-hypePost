<?php

namespace hypeJunction\Fields;

use ElggEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * CustomHtml class.
 */
class CustomHtml extends Field {

	/**
	 * save.
	 *
	 * @param ElggEntity   $entity     entity
	 * @param ParameterBag $parameters parameters
	 *
	 * @return mixed
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		return null;
	}

	/**
	 * retrieve.
	 *
	 * @param ElggEntity $entity entity
	 *
	 * @return mixed
	 */
	public function retrieve(ElggEntity $entity) {
		return null;
	}

	/**
	 * label.
	 *
	 * @param ElggEntity $entity entity
	 *
	 * @return mixed
	 */
	public function label(ElggEntity $entity) {
		return null;
	}

	/**
	 * help.
	 *
	 * @param ElggEntity $entity entity
	 *
	 * @return mixed
	 */
	public function help(ElggEntity $entity) {
		return null;
	}

	/**
	 * placeholder.
	 *
	 * @param ElggEntity $entity entity
	 *
	 * @return mixed
	 */
	public function placeholder(ElggEntity $entity) {
		return null;
	}

	/**
	 * render.
	 *
	 * @param \ElggEntity $entity  entity
	 * @param mixed       $context context
	 *
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
