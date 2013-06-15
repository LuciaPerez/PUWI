<?php
require_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';

class PUWI_GetResults_Test extends PHPUnit_Framework_TestCase{
	
	private $gr;
	
	protected function setUp(){		
		$this->gr = new PUWI_GetResults;
	}
	
	public function testGetProjectName(){
		$path = "ruta/a/proxecto/NomeProxecto/";
		$this->assertEquals('NomeProxecto', $this->gr->getProjectName($path));
	}
	/**
	 * @test
	 */
	public function getTestPassed(){
		$result = new PHPUnit_Framework_TestResult;
		$this->assertInternalType("array",$this->gr->getTestsPassed($result));
	}
	
	public function testGetArrayFolders(){
		$path_project = dirname(dirname(__FILE__));
		$folders = $this->gr->getArrayFolders(dirname(dirname(__FILE__)).'/');
		$this->assertContains('tests/',array_keys($folders));
	}
	
	/*public function testGetResults(){
		
	}*/
}
?>
