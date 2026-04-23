<?php

namespace hypeJunction\Fields;

use Elgg\IntegrationTestCase;

class CollectionTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return '';
	}

	public function up(): void {}

	public function down(): void {}

	private function makeField(string $name, int $priority = 500): Field {
		$field = new MetaField(['priority' => $priority]);
		$field->name = $name;
		return $field;
	}

	public function testAddAndHas(): void {
		$collection = new Collection();
		$collection->add('foo', $this->makeField('foo'));
		$this->assertTrue($collection->has('foo'));
	}

	public function testGetReturnsField(): void {
		$collection = new Collection();
		$field = $this->makeField('bar');
		$collection->add('bar', $field);
		$this->assertSame($field, $collection->get('bar'));
	}

	public function testGetReturnsNullForMissingKey(): void {
		$collection = new Collection();
		$this->assertNull($collection->get('nonexistent'));
	}

	public function testRemove(): void {
		$collection = new Collection();
		$collection->add('baz', $this->makeField('baz'));
		$this->assertTrue($collection->has('baz'));
		$collection->remove('baz');
		$this->assertFalse($collection->has('baz'));
	}

	public function testCount(): void {
		$collection = new Collection();
		$this->assertEquals(0, $collection->count());
		$collection->add('a', $this->makeField('a'));
		$collection->add('b', $this->makeField('b'));
		$this->assertEquals(2, $collection->count());
	}

	public function testSortByPriorityAscending(): void {
		$collection = new Collection();
		$collection->add('high', $this->makeField('high', 100));
		$collection->add('low', $this->makeField('low', 10));
		$collection->add('mid', $this->makeField('mid', 50));
		$collection->sort();

		$names = array_keys($collection->all());
		$this->assertEquals(['low', 'mid', 'high'], $names);
	}

	public function testFilterReturnsSubset(): void {
		$collection = new Collection();
		$collection->add('keep', $this->makeField('keep', 10));
		$collection->add('drop', $this->makeField('drop', 20));

		$filtered = $collection->filter(fn($field) => $field->name === 'keep');
		$this->assertTrue($filtered->has('keep'));
		$this->assertFalse($filtered->has('drop'));
		$this->assertCount(1, $filtered);
	}

	public function testArrayAccess(): void {
		$collection = new Collection();
		$field = $this->makeField('arr');
		$collection['arr'] = $field;
		$this->assertTrue(isset($collection['arr']));
		$this->assertSame($field, $collection['arr']);
		unset($collection['arr']);
		$this->assertFalse(isset($collection['arr']));
	}

	public function testIterator(): void {
		$collection = new Collection();
		$collection->add('x', $this->makeField('x'));
		$collection->add('y', $this->makeField('y'));

		$visited = [];
		foreach ($collection as $key => $field) {
			$visited[] = $key;
		}
		$this->assertEquals(['x', 'y'], $visited);
	}

	public function testAddNamedViaFieldNameProperty(): void {
		$collection = new Collection();
		$field = new MetaField(['priority' => 5]);
		$collection->add('named', $field);
		$this->assertEquals('named', $collection->get('named')->name);
	}
}
