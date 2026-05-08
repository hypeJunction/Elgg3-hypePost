<?php

namespace hypeJunction\Post;

use Elgg\IntegrationTestCase;
use hypeJunction\Fields\Collection;
use hypeJunction\Fields\TitleField;
use hypeJunction\Fields\HtmlField;
use hypeJunction\Fields\AccessField;

class SetObjectFieldsTest extends IntegrationTestCase {

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

		$handler = new SetObjectFields();
		return $handler($hook);
	}

	public function testAddsEightObjectFields(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$this->assertInstanceOf(Collection::class, $result);
		$this->assertCount(8, $result);
	}

	public function testRegistersExpectedFieldNames(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		foreach (['title', 'description', 'excerpt', 'icon', 'cover', 'tags', 'access_id', 'disable_comments'] as $name) {
			$this->assertTrue($result->has($name), "Expected field '$name' not found");
		}
	}

	public function testTitleFieldIsRequired(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$title = $result->get('title');
		$this->assertInstanceOf(TitleField::class, $title);
		$this->assertTrue((bool) $title['required']);
	}

	public function testDescriptionFieldIsRequired(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$desc = $result->get('description');
		$this->assertInstanceOf(HtmlField::class, $desc);
		$this->assertTrue((bool) $desc['required']);
	}

	public function testAccessFieldIsRequired(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);

		$access = $result->get('access_id');
		$this->assertInstanceOf(AccessField::class, $access);
		$this->assertTrue((bool) $access['required']);
	}

	public function testFieldsSortByPriorityAscending(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$result = $this->invokeHandler($entity);
		$result->sort();

		$names = array_keys($result->all());
		$titlePos = array_search('title', $names);
		$descPos = array_search('description', $names);
		$accessPos = array_search('access_id', $names);

		$this->assertLessThan($descPos, $titlePos, 'title (p10) should sort before description (p20)');
		$this->assertLessThan($accessPos, $titlePos, 'title (p10) should sort before access_id (p100)');
	}
}
