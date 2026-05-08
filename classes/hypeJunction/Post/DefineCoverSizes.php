<?php

namespace hypeJunction\Post;

use Elgg\Event;

/**
 * DefineCoverSizes class.
 */
class DefineCoverSizes {

	/**
	 * @elgg_plugin_hook entity:cover:sizes all
	 *
	 * @param Hook $hook Hook
	 *
	 * @return array|null
	 */
	public function __invoke(Event $event) {

			$value = $event->getValue();

		if (!empty($value)) {
			return null;
		}

			return [
				'original' => [],
				'master' => [
					'w' => 1280,
					'h' => 720,
					'square' => false,
					'upscale' => true,
				],
				'large' => [
					'w' => 800,
					'h' => 450,
					'square' => false,
					'upscale' => false,
				],
				'medium' => [
					'w' => 480,
					'h' => 270,
					'square' => false,
					'upscale' => false,
				],
				'small' => [
					'w' => 240,
					'h' => 135,
					'square' => false,
					'upscale' => false,
				],
			];
	}
}
