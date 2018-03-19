<?php

namespace hypeJunction\Post;

use ElggIcon;
use hypeJunction\Scraper\WebResource;

/**
 * Composite cover object
 */
class CoverWrapper {

	/**
	 * @var ElggIcon
	 */
	protected $entity;

	/**
	 * @var string
	 */
	protected $fallback;

	/**
	 * @var array
	 */
	protected $params;

	/**
	 * Contructor
	 *
	 * @param \ElggEntity $entity
	 * @param string      $fallback_url
	 * @param array       $params
	 */
	public function __construct(\ElggEntity $entity, $fallback_url = null, array $params = []) {
		$this->entity = $entity;
		$this->fallback = $fallback_url;
		$this->params = $params;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __get($name) {
		return elgg_extract($name, $this->params);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __set($name, $value) {
		$this->params[$name] = $value;
	}

	/**
	 * @return string|null
	 */
	public function getCoverUrl() {
		$url = $this->entity->getIconURL([
			'type' => 'cover',
			'size' => 'master',
		]);

		if ($url) {
			return $url;
		}

		if ($this->entity->getIcon('master', 'cover')->exists()) {
			return elgg_get_inline_url($this->entity);
		}

		if ($this->fallback && elgg()->has('scraper')) {
			$scraper = elgg()->scraper;
			/* @var $scraper \hypeJunction\Scraper\ScraperService */

			$data = $scraper->scrape($this->fallback);

			if ($data) {
				return elgg_extract('thumbnail_url', $data);
			}
		}

		return $this->fallback;
	}
}
