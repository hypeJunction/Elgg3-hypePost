<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;
use hypeJunction\ValidationException;
use Symfony\Component\HttpFoundation\ParameterBag;

class IconField extends Field {

	/**
	 * {@inheritdoc}
	 */
	public function raw(Request $request, ElggEntity $entity) {

		$files = elgg_get_uploaded_files($this->name);

		if (empty($files)) {
			return null;
		}

		return array_shift($files);
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value) {
		if ($this->required) {
			if ((!$value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
				throw new ValidationException(elgg_echo('validation:error:required'));
			}

			if (!$value->isValid()) {
				throw new ValidationException(elgg_echo('validation:error:invalid_file', [
					elgg_get_friendly_upload_error($value->getError()),
				]));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		return $entity->saveIconFromUploadedFile($this->name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function retrieve(ElggEntity $entity) {
		if (!$entity->guid) {
			return null;
		}

		$icon = $entity->getIcon('master');

		return $icon->exists() ? $icon : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isVisible(ElggEntity $entity, $context = null) {
		$enabled = elgg()->hooks->trigger(
			'uses:icon',
			"$entity->type:$entity->subtype",
			['entity' => $entity],
			false
		);

		if (!$enabled) {
			return false;
		}

		return parent::isVisible($entity, $context);
	}
}