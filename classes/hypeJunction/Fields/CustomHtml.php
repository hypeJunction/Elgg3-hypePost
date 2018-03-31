<?php

namespace hypeJunction\Fields;

use ElggEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

class CustomHtml extends Field {

	public function save(ElggEntity $entity, ParameterBag $parameters) {
		return null;
	}

	public function retrieve(ElggEntity $entity) {
		return null;
	}

	public function label(ElggEntity $entity) {
		return null;
	}

	public function help(ElggEntity $entity) {
		return null;
	}

	public function placeholder(ElggEntity $entity) {
		return null;
	}

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