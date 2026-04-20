<?php

namespace hypeJunction\Post;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	public function boot(): void {
		$parsley = elgg_get_config('path') . 'vendor/bower-asset/parsleyjs/dist/parsley.min.js';
		if (file_exists($parsley)) {
			$this->elgg()->views->registerView('parsley.js', $parsley, 'default');
		}
	}
}
