<?php

namespace hypeJunction\Post;

use Elgg\IntegrationTestCase;

class SocialMenuTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypepost';
	}

	public function up(): void {}

	public function down(): void {}

	private function makeCommentItem(): \ElggMenuItem {
		return \ElggMenuItem::factory([
			'name' => 'comment',
			'text' => 'Comment',
			'href' => '#',
		]);
	}

	private function makeHookWithMenu(\ElggEntity $entity, array $menu): \Elgg\Hook {
		$hook = $this->getMockBuilder(\Elgg\Hook::class)->getMock();
		$hook->method('getEntityParam')->willReturn($entity);
		$hook->method('getValue')->willReturn($menu);
		return $hook;
	}

	public function testCommentItemRemovedWhenCommentsDisabled(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$entity->disable_comments = 1;
		$entity->save();

		$menu = [
			$this->makeCommentItem(),
			\ElggMenuItem::factory(['name' => 'edit', 'text' => 'Edit', 'href' => '#']),
		];

		$handler = new SocialMenu();
		$result = $handler($this->makeHookWithMenu($entity, $menu));

		$names = array_map(fn($item) => $item->getName(), $result);
		$this->assertNotContains('comment', $names);
		$this->assertContains('edit', $names);
	}

	public function testCommentItemKeptWhenCommentsEnabled(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$entity->disable_comments = 0;
		$entity->save();

		$menu = [$this->makeCommentItem()];

		$handler = new SocialMenu();
		$result = $handler($this->makeHookWithMenu($entity, $menu));

		$names = array_map(fn($item) => $item->getName(), $result);
		$this->assertContains('comment', $names);
	}

	public function testReturnsNullWhenNoEntity(): void {
		$hook = $this->getMockBuilder(\Elgg\Hook::class)->getMock();
		$hook->method('getEntityParam')->willReturn(null);

		$handler = new SocialMenu();
		$this->assertNull($handler($hook));
	}
}
