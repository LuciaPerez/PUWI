<?php

class Calculadora{
	public function add($a,$b){
		return $a+$b;
	}

	public function subs($a,$b){
		return $a-$b;
	}

	public function div($a,$b){
		$result = -1;
		if($b > 0){
			$result = $a/$b;
		}
		return $result;
	}

	public function mult($a,$b){
		return $a+$b;
	}

}

?>
