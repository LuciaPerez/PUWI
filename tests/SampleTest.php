<?php

class SampleTest extends PHPUnit_Framework_TestCase {

	private $aVar;

	public function setUp(
	) {
		$this->aVar = 42;
	}

	public function test_setUpWorks(
	) {
		$this->assertEquals(42, $this->aVar);
	}

}
