<?php
require_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';

class GetCodeTest extends PHPUnit_Framework_TestCase{
	
	private $gr;
	
	protected function setUp(){
		$this->gr = new PUWI_GetResults;
	}
	
	protected function tearDown(){
		unset($this->gr);
	}
	
	public function testCheckSearchStartFunctionPattern(){	
		$this->assertRegExp('/function/','function setUpWorks');
	}
	
	public function testCheckSearchEndFunctionPattern(){
		$this->assertRegExp('/\/\*\*/','/**');
	}
	
	public function testReturnCode(){
		$this->assertInternalType("string",$this->gr->getCode(dirname(__FILE__).'/SampleTest.php','test_setUpFails','19'));
	}
}
?>
