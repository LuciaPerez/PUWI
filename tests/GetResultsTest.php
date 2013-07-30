<?php
require_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';

class GetResultsTest extends PHPUnit_Framework_TestCase{

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
		$this->pathProject = dirname(dirname(__FILE__)).'/';
	}
	
	protected function tearDown(){
		unset($this->suite);
		unset($this->gr);
	
	}
	
	public static function tearDownAfterClass()
	{
		self::$suite = NULL;
		self::$result = NULL;
	}
	
	public function testGetProjectName(){
		$path = "ruta/a/proxecto/NomeProxecto/";
		$this->assertEquals('NomeProxecto', $this->gr->getProjectName($path));
	}
	
	public function testgetTestPassed(){
		$this->markTestIncomplete();
		$this->assertInternalType("array",$this->gr->getTestsPassed(self::$result));
	}
	
	public function testgetTestError(){
		$this->assertInternalType("array",$this->gr->getTestsError(self::$result));
	}
	

	public function testgetTestIncompleted(){
		$this->assertInternalType("array",$this->gr->getTestsIncompleted(self::$result));
	}
	

	public function testgetTestFailed(){
		$this->markTestSkipped();
		$this->assertInternalType("array",$this->gr->getTestsFailed(self::$result));
	}
	

	public function testgetTestSkipped(){
		$this->assertInternalType("array",$this->gr->getTestsSkipped(self::$result));
	}

	public function testOrderGroups(){
		$array = array ("a"=>"letterA","c"=>"letterC","b"=>"letterB");
		krsort($array);
		$this->assertEquals(array("c"=>"letterC","b"=>"letterB","a"=>"letterA"), $array);
	}
	
	public function testCheckArrayResults(){
		$argv = array("",dirname(dirname(__FILE__)));
		$arrayResults = $this->gr->getResults(dirname(dirname(__FILE__)),self::$result,$argv,self::$suite);
		
		$keys = array('projectName','passed','failures','errors','incompleted','skipped','groups','folders','failedTests');
		
		$this->assertEquals($keys,array_keys($arrayResults));
	}
	
	public function testReturnTypeProjectName(){
		$argv = array("",dirname(dirname(__FILE__)));
		$arrayResults = $this->gr->getResults(dirname(dirname(__FILE__)),self::$result,$argv,self::$suite);
	
		$this->assertInternalType("string",$arrayResults['projectName']);
	}
	
	public function testCheckTestsName(){
		self::$suite->addTestFile(dirname(__FILE__).'/../SampleTest.php');
		$name_groups = $this->gr->getGroups(self::$suite->getGroupDetails(), self::$suite->getGroups());

		foreach (array_values($name_groups) as $content){
			foreach($content as $test){
				$this->assertRegExp('/.*::.*/',$test);
			}
		}
	}
	
	//Get Fails Tests
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

	//Get Folders Tests
	public function testGetFolders(){
		$this->array_folders = $this->gr->getArrayFolders($this->pathProject);
		unset($this->array_folders[0]);
		$this->assertArrayHasKey('tests/',$this->array_folders);
	}
	
	public function testArrayFoldersIsArray(){
		$this->assertInternalType("array",$this->array_folders);	
	}
	
	public function testCheckIsDir(){
		$this->fail();
		$this->pathProject = dirname(dirname(__FILE__)).'/PUWI_GetResults.php';
		$this->array_folders = $this->gr->getArrayFolders($this->pathProject);
		$this->assertEmpty($this->array_folders);
	}
	
	public function testGetFolderName(){
		$this->assertEquals('tests/',substr($this->pathProject.'tests/',strlen($this->pathProject)));
	}


	
}
?>
