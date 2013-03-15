<?php
require('puwi/setup.php');

class index{
	private $showedClass='';

	function receive_array($url_array) { 
	    $tmp = stripslashes($url_array); 
	    $tmp = urldecode($tmp); 
	    $tmp = unserialize($tmp); 
	    return $tmp; 
	} 

	function get_URLData($data){
		$received = $_GET[$data];
		return $this->receive_array($received); 
	}

	function getClassNameTest($value, $passed, $incomplete, $skipped){
		if(in_array($value,$passed)){
			$classNameTest="testOK box";} 
		else if((in_array($value,$incomplete)) || (in_array($value,$skipped))) {
			$classNameTest="testIncomplete box";
		}else{ $classNameTest="testFailed box"; }

		return $classNameTest;
	}

	function is_showedClass($class){
		if($this->showedClass == $class){
			return 'no';
		}else{
			$this->showedClass=$class;
			return 'yes';
		}
		return $this->$is_showedClass;
	}


	public static function main($exit = TRUE)
	{
		$index = new index();

		$passed = $index->get_URLData('passed'); 
		$failures = $index->get_URLData('failures');
		$errors = $index->get_URLData('errors');
		$incomplete = $index->get_URLData('incomplete');
		$skipped = $index->get_URLData('skipped');
		$groups = $index->get_URLData('groups');

		$totalTestsReceived = array_merge($passed,$failures,$incomplete,$skipped,$errors);
		sort($totalTestsReceived);

		$smarty = new Smarty_Puwi();

		$smarty->display("header.tpl");
		$smarty->display("results.tpl");

		foreach ($totalTestsReceived as $key => $value){  
			$class=strstr($value, ':', true);
			$test=substr(strrchr($value, ":"), 1);
	
			$className="classTest";	
			
			$createClassNameDiv = $index->is_showedClass($class);

			$smarty->assign("createClassNameDiv",$createClassNameDiv);
			$smarty->assign("className",$className);
			$smarty->assign("class",$class);

			$classNameTest = $index->getClassNameTest($value, $passed, $incomplete, $skipped);

			$smarty->assign("classNameTest",$classNameTest);
			$smarty->assign("test",$test);	

			$smarty->display("tests.tpl");

			$smarty->clear_assign(array('className', 'class', 'classNameTest', 'test'));
			$smarty->clear_cache('tests.tpl');
		}

		$smarty->display("footer.tpl");
		$smarty->clear_all_assign();
		$smarty->clear_all_cache();

	}

}

index::main();




?>

