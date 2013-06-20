<?php
require_once dirname(dirname(__FILE__)).'/PUWI_Command.php';

class PUWICommandTest extends PHPUnit_Framework_TestCase{
	
	private $command;
	
	protected function setUp(){
		$this->command = new PUWI_Command;
	}
	
	protected function tearDown(){
		unset($this->command);
	}
	
	public function testGroupOfTests(){
		$actionStartTest = 'mv '.dirname(dirname(__FILE__)).'/tests/Calculadora '.dirname(dirname(dirname(__FILE__))).'/';
		exec($actionStartTest);
	
		$argv = array(dirname(__FILE__).'/PUWI_Command.php',dirname(dirname(dirname(__FILE__))).'/Calculadora/','CalculadoraTest','file');
		$this->command->run($argv);
		$this->assertGreaterThan(3,sizeof($argv));
		
		$actionEndTest = 'mv '.dirname(dirname(dirname(__FILE__))).'/Calculadora '.dirname(dirname(__FILE__)).'/tests/';
		exec($actionEndTest);
	}
	
	
	public function testRunAllTests(){
		$actionStartTest = 'mv '.dirname(dirname(__FILE__)).'/tests/Calculadora '.dirname(dirname(dirname(__FILE__))).'/';
		exec($actionStartTest);

		$argv = array(dirname(__FILE__).'/PUWI_Command.php',dirname(dirname(dirname(__FILE__))).'/Calculadora/');
		$this->command->run($argv);
		$this->assertLessThan(3,sizeof($argv));
		
		$actionEndTest = 'mv '.dirname(dirname(dirname(__FILE__))).'/Calculadora '.dirname(dirname(__FILE__)).'/tests/';
		exec($actionEndTest);
	}

}
?>
