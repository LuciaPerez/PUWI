<?php

class PUWI_LaunchBrowser{

	/*
	*@param integer $totalTests
	*@param string  $projectName
	*/

	public function launchBrowser($totalTests,$projectName,$passed,$failures,$incomplete,$skipped){
				
		$passed=PUWI_LaunchBrowser::send_array($passed); 
		$failures=PUWI_LaunchBrowser::send_array($failures);
		$incomplete=PUWI_LaunchBrowser::send_array($incomplete);
		$skipped=PUWI_LaunchBrowser::send_array($skipped);
	
		$url="http://localhost/view/puwi.php"."?projectName=".$projectName."\&totalTests=".$totalTests."\&passed=".$passed."\&failures=".$failures."\&incomplete=".$incomplete."\&skipped=".$skipped;

		$command="x-www-browser ".$url." &";
		system($command);

	}

	function getResults($projectName,$result){
		$totalTests = $result->count();
		$projectName=PUWI_LaunchBrowser::getProjectName($projectName);

		$passed=PUWI_LaunchBrowser::getTestsPassed($result);
		$failures=PUWI_LaunchBrowser::getTestsFailed($result);
		$incomplete=PUWI_LaunchBrowser::getTestsIncompleted($result);
		$skipped=PUWI_LaunchBrowser::getTestsSkipped($result);

		PUWI_LaunchBrowser::launchBrowser($totalTests,$projectName,$passed,$failures,$incomplete,$skipped);

	}


	function send_array($array) { 
	    $tmp = serialize($array); 
	    $tmp = urlencode($tmp); 
	    return $tmp; 
	}
	
	public function getProjectName($projectName){
		$names=preg_split("/[\/]tests/",$projectName);
		$projectName=explode("/",$names[0]);
		$size=sizeof($projectName);
		return $projectName[$size-1];
	}

	function getTestsPassed(PHPUnit_Framework_TestResult $result){
		$r=$result->passed();
		$passed=array_keys($r);
		return($passed);
	} 

	function getTestsFailed(PHPUnit_Framework_TestResult $result){
		return(PUWI_LaunchBrowser::getClassAndNameTest($result->failures()));
	}

	function getTestsIncompleted(PHPUnit_Framework_TestResult $result){
		return(PUWI_LaunchBrowser::getClassAndNameTest($result->notImplemented()));
	}

	function getTestsSkipped(PHPUnit_Framework_TestResult $result){
		return(PUWI_LaunchBrowser::getClassAndNameTest($result->skipped()));
	}
	
	function getClassAndNameTest(array $tests){
		$result = array();
		foreach ($tests as $test){
			$t=$test->failedTest();
			$class=get_class($t);
			$name=$t->getName();
			$fullName=$class."::".$name;
			array_push($result,$fullName);
		}
		return($result);
	}
}
?>
