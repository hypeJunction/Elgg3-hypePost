<?php

namespace hypeJunction\Post;

use Elgg\IntegrationTestCase;
use hypeJunction\Fields\Collection;
use hypeJunction\Fields\ProfileModulesField;

class AddProfileModulesFieldTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypepost';
	}

	public function up(): void {}

	public function down(): void {}

	private function invokeHandler(\ElggEntity $entity): Collection {
		$collection = new Collection();
		$hook = $this->getMockBuilder(\Elgg\Event::class)->disableOriginalConstructor()->getMock();
		$hook->method('getValue')->willReturn($collection);
		$hook->method('getEntityParam')->willReturn($entity);

		$handler = new AddProfileModulesField();
		return $handler($hook);
	}

	public function testAddsModulesFieldForObject(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$this->assertTrue($result->has('modules'));
		$this->assertInstanceOf(ProfileModulesField::class, $result->get('modules'));
	}

	public function testModulesFieldIsCreateFieldForObject(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$modules = $result->get('modules');
		$this->assertTrue((bool) $modules['is_create_field']);
	}

	public function testModulesFieldIsNotCreateFieldForUser(): void {
		$user = $this->createUser();
		$result = $this->invokeHandler($user);

		$modules = $result->get('modules');
		$this->assertFalse((bool) $modules['is_create_field']);
	}

	public function testModulesFieldHasSidebarSection(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$modules = $result->get('modules');
		$this->assertEquals('sidebar', $modules['section']);
	}
}
