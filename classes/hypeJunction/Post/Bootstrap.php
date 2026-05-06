<?php

namespace hypeJunction\Post;

use Elgg\DefaultPluginBootstrap;

/**
 * Bootstrap class.
 */
class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * boot.
	 *
	 * @return void
	 */
	public function boot(): void {
		$parsley = elgg_get_config('path') . 'vendor/bower-asset/parsleyjs/dist/parsley.min.js';
		if (file_exists($parsley)) {
			$this->elgg()->views->registerView('parsley.js', $parsley, 'default');
		}
	}
}
