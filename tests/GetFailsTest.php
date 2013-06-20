<?php
require_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';

class GetFailsTest extends PHPUnit_Framework_TestCase{
	
	private $gr;
	private static $suite;
	private static $result;

	public static function setUpBeforeClass()
	{
		self::$suite = new PHPUnit_Framework_TestSuite;
		
		self::$suite->addTestFile(dirname(__FILE__).'/SampleTest.php');
		self::$result = self::$suite->run();
		
	}
	
	
	
	protected function setUp(){
		$this->gr = new PUWI_GetResults;
	}
	
	protected function tearDown(){
		unset($this->gr);
	}
	
	public static function tearDownAfterClass()
	{
		self::$suite = NULL;
		self::$result = NULL;
	}
	
	public function testGetFailsReturnType(){
		print_r(self::$suite->getName());
		$this->gr->getFails(self::$result->failures());	
		$this->assertInternalType("array",$this->gr->getInfoFailedTests());
	}
	
	public function testIndexArray(){	
		$this->gr->getFails(self::$result->failures());
		$keys = array("testName","file","line","message","code","trace");
		foreach ($this->gr->getInfoFailedTests() as $result_keys){
			$this->assertEquals($keys, array_keys($result_keys));
		}
	}
	

}


?>
