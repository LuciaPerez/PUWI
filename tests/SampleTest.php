<?php

class SampleTest extends PHPUnit_Framework_TestCase {

	private $aVar;

	public function setUp(
	) {
		$this->aVar = 42;
	}
	/**
	* @group groupSampleTest
	*/
	public function test_setUpWorks(
	) {
		$this->assertEquals(42, $this->aVar);
	}

	public function test_setUpFails(
	) {
		$this->fail("This test of SampleTest class fails");
	}

}

?>
