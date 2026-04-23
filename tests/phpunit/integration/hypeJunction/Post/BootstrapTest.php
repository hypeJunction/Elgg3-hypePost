<?php

namespace hypeJunction\Post;

use Elgg\IntegrationTestCase;

class BootstrapTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypepost';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPluginIsActive(): void {
		$plugin = elgg_get_plugin_from_id('hypepost');
		$this->assertInstanceOf(\ElggPlugin::class, $plugin);
		$this->assertTrue($plugin->isActive());
	}

	public function testPostServiceRegistered(): void {
		$this->assertInstanceOf(Post::class, elgg()->get('posts.post'));
	}

	public function testPostInstanceReturnsPostService(): void {
		$instance = Post::instance();
		$this->assertInstanceOf(Post::class, $instance);
	}

	public function testCoverSizesHookIsRegistered(): void {
		$fired = false;
		elgg_register_plugin_hook_handler('entity:cover:sizes', 'all', function () use (&$fired) {
			$fired = true;
		}, 999);
		elgg_trigger_plugin_hook('entity:cover:sizes', 'all', [], []);
		$this->assertTrue($fired);
	}
}
