<?php
require_once dirname(dirname(__FILE__)).'/PUWI_GetResults.php';


class GetFoldersTest extends PHPUnit_Framework_TestCase{
	private $gr;
	private $pathProject;
	private $array_folders = array();
	
	protected function setUp(){
		$this->gr = new PUWI_GetResults;
		$this->pathProject = dirname(dirname(__FILE__)).'/';
	}
	
	public function testGetFolders(){
		$this->array_folders = $this->gr->getArrayFolders($this->pathProject);
		unset($this->array_folders[0]);
		$this->assertArrayHasKey('tests/',$this->array_folders);
	}
	
	public function testArrayFoldersIsArray(){
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
	
	protected function tearDown(){
		unset($this->gr);
	}
}
?>
