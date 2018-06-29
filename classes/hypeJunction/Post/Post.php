<?php

namespace hypeJunction\Post;

use Elgg\Di\ServiceFacade;
use ElggEntity;

class Post {

	use ServiceFacade;

	/**
	 * {@inheritdoc}
	 */
	public static function name() {
		return 'posts.post';
	}

	/**
	 * Display comment block
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return bool
	 */
	public function hasCommentBlock(ElggEntity $entity) {
		$params = ['entity' => $entity];

		return elgg_trigger_plugin_hook(
			'uses:comments',
			"$entity->type:$entity->subtype",
			$params,
			!$entity->disable_comments
		);
	}

	/**
	 * Get open graph and other metatags
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return mixed
	 */
	public function getOpenGraphProperties(ElggEntity $entity) {
		$metadata = elgg_trigger_plugin_hook('metatags', 'discovery', [
			'entity' => $entity,
		], []);

		return $metadata;
	}

	/**
	 * Set page metatags for this entity
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return void
	 */
	public function setPageMetatags(ElggEntity $entity) {
		elgg_register_plugin_hook_handler('head', 'page', function (\Elgg\Hook $hook) use ($entity) {
			$value = $hook->getValue();

			$value['title'] = $entity->getDisplayName();

			$metatags = $this->getOpenGraphProperties($entity);

			if (!empty($metatags) && is_array($metatags)) {
				foreach ($metatags as $name => $content) {
					if (!$content) {
						continue;
					}
					$name_parts = explode(':', $name);
					$namespace = array_shift($name_parts);
					$ogp = ['og', 'fb', 'article', 'profile', 'book', 'music', 'video', 'profile', 'website'];
					if (in_array($namespace, $ogp)) {
						// OGP tags use 'property=""' attribute
						$value['metas'][$name] = [
							'property' => $name,
							'content' => $content,
						];
					} else {
						$value['metas'][$name] = [
							'name' => $name,
							'content' => $content,
						];
					}
				}
			}

			return $value;
		});
	}

	/**
	 * Get cover object
	 *
	 * @param ElggEntity $entity   Entity
	 * @param bool       $fallback Fallback to content images
	 *
	 * @return CoverWrapper
	 */
	public function getCover(ElggEntity $entity, $fallback = false) {
		$url = '';
		$params = [];
		if ($entity->{'cover:file_url'}) {
			$url = $entity->{'cover:file_url'};

			$fields = [
				'uid',
				'file_url',
				'thumb_url',
				'author',
				'author_url',
				'provider',
				'provider_url',
				'license',
				'copyright',
				'disclaimer',
				'attribution',
				'gravity',
				'ratio',
				'color',
				'width',
				'height',
			];

			foreach ($fields as $field) {
				$params[$field] = $entity->{"cover:$field"};
			}
		} else if ($entity->web_location) {
			$url = $entity->web_location;
		} else if ($entity instanceof \ElggFile) {
			$url = elgg_get_inline_url($entity->getIcon('large'));
		} else if ($fallback) {
			$description = elgg_view('output/longtext', [
				'value' => $entity->description,
			]);

			$html = elgg_format_element('div', [], $description);

			try {
				$doc = new \DOMDocument();

				libxml_use_internal_errors(true);

				if (is_callable('mb_convert_encoding')) {
					$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
				} else {
					$doc->loadHTML($html);
				}

				$nodes = $doc->getElementsByTagName('img');
				foreach ($nodes as $node) {
					$url = $node->getAttribute('src');
					break;
				}
			} catch (\Exception $ex) {
			}

			libxml_clear_errors();
		}

		return new CoverWrapper(
			$entity,
			$url,
			$params
		);
	}

	/**
	 * Returns current entity attributes and metadata
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return array
	 */
	public function captureState(ElggEntity $entity) {
		$state = [];
		foreach ($entity as $key => $value) {
			$state[$key] = $value;
		}

		$metadata = elgg_get_metadata([
			'guids' => (int) $entity->guid,
			'limit' => 0,
			'batch' => true,
			'batch_inc_size' => 50,
		]);

		foreach ($metadata as $md) {
			$state[$md->name] = $md->value;
		}

		return $state;
	}

	/**
	 * Save current state as annotation
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function logHistory(ElggEntity $entity) {
		elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity) {
			$state = json_encode($this->captureState($entity));
			$entity->annotate('edit_history', $state, ACCESS_PRIVATE);
		});
	}

	/**
	 * Get excerpt
	 *
	 * @param ElggEntity $entity Entity
	 * @param int        $length Length
	 *
	 * @return string
	 */
	public function getExcerpt(ElggEntity $entity, $length = 250) {
		if ($entity->excerpt) {
			return elgg_get_excerpt($entity->excerpt, $length);
		} else {
			return elgg_get_excerpt($entity->description, $length);
		}
	}

	/**
	 * Get template view
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string
	 */
	public function getTemplate(\ElggEntity $entity) {
		$params = [
			'entity' => $entity,
		];

		$default = $entity->template ? : 'default';

		return elgg_trigger_plugin_hook('template', "$entity->type:$entity->subtype", $params, $default);
	}

	/**
	 * Get layout modules
	 *
	 * <code>
	 * [
	 *    'name' => [
	 *        'enabled' => true,
	 *        'position' => 'sidebar',
	 *        'priority' => 300,
	 *    ],
	 * ]
	 * </code>
	 *
	 * @param ElggEntity $entity Entity
	 * @param string $position Position
	 *
	 * @return string[]
	 */
	public function getModules(\ElggEntity $entity, $position = null) {

		$params = [
			'entity' => $entity,
		];

		$modules = elgg_trigger_plugin_hook('modules', "$entity->type", $params, []);
		$modules = elgg_trigger_plugin_hook('modules', "$entity->type:$entity->subtype", $params, $modules);

		$modules = array_filter($modules, function($e) use ($position) {
			if (isset($position) && elgg_extract('position', $e) !== $position) {
				return false;
			}

			if (elgg_extract('enabled', $e) === false) {
				return false;
			}

			return true;
		});

		uasort($modules, function ($md1, $md2) {
			$p1 = (int) elgg_extract('priority', $md1, 500);
			$p2 = (int) elgg_extract('priority', $md2, 500);
			if ($p1 === $p2) {
				return 0;
			}

			return $p1 < $p2 ? -1 : 1;
		});

		return $modules;
	}

	/**
	 * Log entity view
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return void
	 */
	public function logView(\ElggEntity $entity) {
		elgg_trigger_event('view', $entity->type, $entity);
	}
}
