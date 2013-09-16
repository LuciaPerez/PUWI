<?php
require_once dirname(__FILE__).'/../src/Calcu.php';
class CalculadoraTest extends PHPUnit_Framework_TestCase
{
	private $c;

	protected function setUp(){
		$this->c = new Calculadora();
	}



	/**
	* @group grupo_Suma
	*/
	public function testAdd(){
		$this->assertEquals(6,$this->c->add(2, 4));
	}

	/**
	* @group grupo_Resta
	*/
	public function testSubs(){
		$this->assertEquals(3,$this->c->subs(15, 12));
	}

	/**
	* @group grupo_Division
	*/
	public function testDivision(){
		$this->assertEquals(5,$this->c->div(15, 3));
	}

	/**
	* @group grupo_Division
	* @test
	*/
	public function Divisor(){
		$this->assertEquals(-2,$this->c->div(15, 0));
	}

	/**
	* @group grupo_Multiplicacion
	*/
	public function testMult(){
		$this->assertEquals(18,$this->c->mult(3, 6));
	}

	public function testInConstruction(){
		$this->markTestIncomplete();
	}




}
?>

