<?php
require_once dirname(dirname(__FILE__)).'/PUWI_Runner.php';
require_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';

class PUWIRunnerTest extends PHPUnit_Framework_TestCase{
	
	private $runner;
	private static $suite;
	private $arguments;

	
	public static function setUpBeforeClass()
	{
		self::$suite = new PHPUnit_Framework_TestSuite;
		self::$suite->addTestFile(dirname(__FILE__).'/SampleTest.php');
	}

	protected function setUp(){
		$this->runner = new PUWI_Runner;
		$this->arguments = Array(
				"useDefaultConfiguration" => 1,
				"testSuffixes" => Array(
						0 => 'Test.php',
						1 => '.phpt'
				),
				"configuration" => dirname(dirname(__FILE__)).'/tests/Calculadora/phpunit.xml.dist'			
		);
	}    
	
	protected function tearDown(){
		unset($this->runner);
	}
	
	public static function tearDownAfterClass(){
		self::$suite = NULL;
	}
	
	public function testRunSingleTest(){
		$argv = array("","","test_setUpFails","test");
		$this->assertInstanceOf("PHPUnit_Framework_TestResult",$this->runner->doRunSingleTest(self::$suite,$argv,$this->arguments));
	}
	
	public function testRunFile(){
		$argv = array("","","SampleTest","file");
		$this->assertInstanceOf("PHPUnit_Framework_TestResult",$this->runner->doRunSingleTest(self::$suite,$argv,$this->arguments));
	}
	
	public function testRunGroup(){
		$argv = array("","","groupSampleTest","group");
		$this->assertInstanceOf("PHPUnit_Framework_TestResult",$this->runner->doRunSingleTest(self::$suite,$argv,$this->arguments));
	}
	
}
?>
