<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;
use hypeJunction\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

class CoverField extends Field {

	/**
	 * {@inheritdoc}
	 */
	public function raw(Request $request, ElggEntity $entity) {
		$files = elgg_get_uploaded_files($this->name);
		$cover = $request->getParam($this->name, []);

		return [
			'file' => array_shift($files),
			'url' => elgg_extract('url', $cover),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value) {
		if ($this->required) {
			if ((!$value['file'] instanceof UploadedFile) && empty($value['url'])) {
				throw new ValidationException(elgg_echo('validation:error:required'));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters) {
		$value = $parameters->get($this->name);

		$file = elgg_extract('file', $value);
		$url = elgg_extract('url', $value);

		if ($file || $url) {
			$props = [
				'uid',
				'file_url',
				'thumb_url',
				'color',
				'width',
				'height',
				'provider',
				'provider_url',
				'author',
				'author_url',
				'gravity',
				'ratio',
			];

			foreach ($props as $prop) {
				unset($entity->{"cover:$prop"});
			}
		}

		if ($file instanceof UploadedFile && $file->isValid()) {
			$tmp_filename = time() . $file->getClientOriginalName();
			$tmp = new \ElggFile();
			$tmp->owner_guid = $entity->guid;
			$tmp->setFilename("tmp/$tmp_filename");
			$tmp->open('write');
			$tmp->close();

			copy($file->getPathname(), $tmp->getFilenameOnFilestore());

			$entity->saveIconFromElggFile($tmp, 'cover');

			$tmp->delete();
		} else if ($url) {
			$bytes = file_get_contents($url);

			if (!empty($bytes)) {
				$tmp = new \ElggFile();
				$tmp->owner_guid = $entity->guid;
				$tmp->setFilename("tmp/" . pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME));

				$tmp->open('write');
				$tmp->write($bytes);
				$tmp->close();

				$entity->saveIconFromElggFile($tmp, 'cover');

				$tmp->delete();
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function retrieve(ElggEntity $entity) {

		if (!$entity->guid) {
			return null;
		}
		$svc = \hypeJunction\Post\Post::instance();
		/* @var $svc \hypeJunction\Post\Post */

		$cover = $svc->getCover($entity);

		if ($cover->getCoverUrl()) {
			return $cover;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function isVisible(ElggEntity $entity, $context = null) {
		$enabled = elgg()->hooks->trigger(
			'uses:cover',
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