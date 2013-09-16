<?php
include_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';

class GetResultsTest extends PHPUnit_Framework_TestCase{

	private $gr;
	private static $suite;
	private static $result;
	private static $coverage_path;

	public static function setUpBeforeClass()
	{
		self::$suite = new PHPUnit_Framework_TestSuite;
		self::$coverage_path = dirname(dirname(__FILE__)).'/tests/Calculadora/results-coverage';
		self::$suite->addTestFile(dirname(__FILE__).'/Calculadora/tests/CalculadoraTest.php');
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
		$this->assertInternalType("array",$this->gr->getTestsPassed(self::$result));
	}
	
	public function testgetTestError(){
		$this->assertInternalType("array",$this->gr->getTestsError(self::$result));
	}
	

	public function testgetTestIncompleted(){
		$this->assertInternalType("array",$this->gr->getTestsIncompleted(self::$result));
	}
	

	public function testgetTestFailed(){
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
		$argv = array(dirname(dirname(__FILE__)),dirname(__FILE__).'/Calculadora');
		$arrayResults = $this->gr->getResults(dirname(dirname(__FILE__)),self::$result,$argv,self::$suite,self::$coverage_path);
		
		$keys = array('projectName','passed','failures','errors','incomplete','skipped','groups','folders','failedTests','coverage');
		
		$this->assertEquals($keys,array_keys($arrayResults));
	}
	
	public function testReturnTypeProjectName(){
		$argv = array(dirname(dirname(__FILE__)),dirname(__FILE__).'/Calculadora');
		$arrayResults = $this->gr->getResults(dirname(dirname(__FILE__)),self::$result,$argv,self::$suite,self::$coverage_path);
	
		$this->assertInternalType("string",$arrayResults['projectName']);
	}
	
	public function testCheckTestsName(){
		self::$suite->addTestFile(dirname(__FILE__).'/SampleTest.php');
		$name_groups = $this->gr->getGroups(self::$suite->getGroupDetails(), self::$suite->getGroups());

		foreach (array_values($name_groups) as $content){
			foreach($content as $test){
				$this->assertRegExp('/.*::.*/',$test);
			}
		}
	}
	
	//Get Fails Tests
	public function testGetFailsReturnType(){
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
		$this->array_folders = $this->gr->getArrayFolders($this->pathProject);
		$this->assertInternalType("array",$this->array_folders);	
	}
	
	public function testCheckIsDir(){
		$this->pathProject = dirname(dirname(__FILE__)).'/PUWI_GetResults.php';
		$this->array_folders = $this->gr->getArrayFolders($this->pathProject);
		$this->assertEmpty($this->array_folders);
	}
	
	public function testGetFolderName(){
		$this->assertEquals('tests/',substr($this->pathProject.'tests/',strlen($this->pathProject)));
	}

	//Coverage analysis
	public function testGetCoverageLocation(){
		$coverage_locacion = dirname(dirname(__FILE__)).'/COVERAGE-PUWI';	
		$this->assertTrue(is_dir($coverage_locacion));
	}

	public function testCoverageURL(){
		$argv = array(dirname(dirname(__FILE__)),dirname(__FILE__).'/Calculadora');
		$arrayResults = $this->gr->getResults(dirname(dirname(__FILE__)),self::$result,$argv,self::$suite,self::$coverage_path);
		
		$expected_url = "http://localhost/PUWI/tmp/Calculadora/index.html";
		
		$this->assertEquals($expected_url,$arrayResults['coverage']);
	}
	
}
?>
