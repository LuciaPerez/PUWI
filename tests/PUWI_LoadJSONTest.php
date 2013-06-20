<?php
include_once dirname(dirname(__FILE__)).'/PUWI_LoadJSON.php';

class PUWI_LoadJSONTest extends PHPUnit_Framework_TestCase{
	
	protected function setUp(){
		ob_start();
		$actionStartTest = 'mv '.dirname(dirname(__FILE__)).'/tests/Calculadora '.dirname(dirname(dirname(__FILE__))).'/';
		exec($actionStartTest);		
	}
	
	protected function tearDown(){
		$actionEndTest = 'mv '.dirname(dirname(dirname(__FILE__))).'/Calculadora '.dirname(dirname(__FILE__)).'/tests/';
		exec($actionEndTest);
	}

	public function testCreateCommand(){
		$this->assertInstanceOf("PUWI_Command",createCommand());
	}
	
	public function testEncodeJson(){
		
		$data = array("a","b","c");
		$sended = array ("result" => $data);
		sendData($data);
		$encoded_data = ob_get_clean();
	
		$this->assertJsonStringEqualsJsonString(json_encode($sended),$encoded_data);
	}
	
	public function testRunAllTests(){	
		$url_params = array(dirname(__FILE__).'/PUWI_Command.php',dirname(dirname(dirname(__FILE__))).'/Calculadora/');
		selectRunner('rerun',$url_params);
	}
	
	public function testRunFolder(){
		$url_params = array(dirname(__FILE__).'/PUWI_Command.php',dirname(dirname(dirname(__FILE__))).'/Calculadora/tests/');
		selectRunner('runAllTests',$url_params);
	}
	
	public function testGroupOfTests(){		
		$url_params = array(dirname(__FILE__).'/PUWI_Command.php',dirname(dirname(dirname(__FILE__))).'/Calculadora/');
		runCommand('CalculadoraTest','file',$url_params);
	}
}
?>
