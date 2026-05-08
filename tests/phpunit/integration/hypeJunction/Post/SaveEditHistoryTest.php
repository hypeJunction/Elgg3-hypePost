<?php

namespace hypeJunction\Post;

use Elgg\IntegrationTestCase;

class SaveEditHistoryTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypepost';
	}

	public function up(): void {}

	public function down(): void {}

	private function invokeHandler(\ElggEntity $entity): void {
		$event = $this->getMockBuilder(\Elgg\Event::class)->disableOriginalConstructor()->getMock();
		$event->method('getObject')->willReturn($entity);

		$handler = new SaveEditHistory();
		$handler($event);
	}

	public function testAnnotationCreatedByHandler(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);

		$before = (int) elgg_get_annotations([
			'guid' => $entity->guid,
			'annotation_name' => 'edit_history',
			'count' => true,
		]);

		$this->invokeHandler($entity);

		$after = (int) elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity) {
			return elgg_get_annotations([
				'guid' => $entity->guid,
				'annotation_name' => 'edit_history',
				'count' => true,
			]);
		});

		$this->assertGreaterThan($before, $after);
	}

	public function testAnnotationContainsSerializedEntityState(): void {
		$entity = $this->createObject(['subtype' => 'test_post']);
		$entity->title = 'History State ' . $entity->guid;
		$entity->save();

		$this->invokeHandler($entity);

		$annotations = elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity) {
			return elgg_get_annotations([
				'guid' => $entity->guid,
				'annotation_name' => 'edit_history',
				'limit' => 1,
			]);
		});

		$this->assertNotEmpty($annotations);

		$state = json_decode($annotations[0]->value, true);
		$this->assertIsArray($state);
		$this->assertArrayHasKey('guid', $state);
		$this->assertEquals($entity->guid, (int) $state['guid']);
	}

	public function testHandlerIgnoresNonEntityObjects(): void {
		$event = $this->getMockBuilder(\Elgg\Event::class)->disableOriginalConstructor()->getMock();
		$event->method('getObject')->willReturn('not an entity');

		$handler = new SaveEditHistory();
		$handler($event);

		$this->assertTrue(true);
	}
}
