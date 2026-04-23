<?php

namespace hypeJunction\Post;

use Elgg\IntegrationTestCase;

class DefineCoverSizesTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypepost';
	}

	public function up(): void {}

	public function down(): void {}

	private function makeHook(array $currentValue): \Elgg\Hook {
		$hook = $this->getMockBuilder(\Elgg\Hook::class)->getMock();
		$hook->method('getValue')->willReturn($currentValue);
		return $hook;
	}

	public function testDefinesFiveSizesWhenValueEmpty(): void {
		$handler = new DefineCoverSizes();
		$result = $handler($this->makeHook([]));

		$this->assertIsArray($result);
		$this->assertCount(5, $result);
		$this->assertArrayHasKey('original', $result);
		$this->assertArrayHasKey('master', $result);
		$this->assertArrayHasKey('large', $result);
		$this->assertArrayHasKey('medium', $result);
		$this->assertArrayHasKey('small', $result);
	}

	public function testSkipsWhenValueAlreadySet(): void {
		$handler = new DefineCoverSizes();
		$result = $handler($this->makeHook(['existing' => []]));

		$this->assertNull($result);
	}

	public function testMasterSizeHasCorrect16x9Dimensions(): void {
		$handler = new DefineCoverSizes();
		$result = $handler($this->makeHook([]));

		$master = $result['master'];
		$this->assertEquals(1280, $master['w']);
		$this->assertEquals(720, $master['h']);
		$this->assertTrue($master['upscale']);
		$this->assertFalse($master['square']);
	}

	public function testLargerSizesDoNotUpscale(): void {
		$handler = new DefineCoverSizes();
		$result = $handler($this->makeHook([]));

		foreach (['large', 'medium', 'small'] as $size) {
			$this->assertFalse($result[$size]['upscale'], "$size should not upscale");
			$this->assertFalse($result[$size]['square'], "$size should maintain aspect ratio");
		}
	}
}
