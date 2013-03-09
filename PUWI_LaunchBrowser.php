<?php

class PUWI_LaunchBrowser{

	/*
	*@param integer $totalTests
	*@param string  $projectName
	*/

	public function launchBrowser($totalTests,$projectName,$passed,$failures,$errors,$incomplete,$skipped){
				
		$passed=PUWI_LaunchBrowser::send_array($passed); 
		$failures=PUWI_LaunchBrowser::send_array($failures);
		$errors=PUWI_LaunchBrowser::send_array($errors);
		$incomplete=PUWI_LaunchBrowser::send_array($incomplete);
		$skipped=PUWI_LaunchBrowser::send_array($skipped);
	
		$url="http://localhost/view/index.php".
		     "?projectName=".$projectName.
		     "\&totalTests=".$totalTests.
		     "\&passed=".$passed.
		     "\&failures=".$failures.
		     "\&errors=".$errors.
		     "\&incomplete=".$incomplete.
		     "\&skipped=".$skipped;

		$command="x-www-browser ".$url." &";
		system($command);

	}

	function getResults($projectName,$result){
		$totalTests = $result->count();
		$projectName=PUWI_LaunchBrowser::getProjectName($projectName);

		$passed=PUWI_LaunchBrowser::getTestsPassed($result);
		$failures=PUWI_LaunchBrowser::getTestsFailed($result);
		$errors=PUWI_LaunchBrowser::getTestsError($result);
		$incomplete=PUWI_LaunchBrowser::getTestsIncompleted($result);
		$skipped=PUWI_LaunchBrowser::getTestsSkipped($result);

		PUWI_LaunchBrowser::launchBrowser($totalTests,$projectName,$passed,$failures,$errors,$incomplete,$skipped);

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
		$fail=$result->failures();
		PUWI_LaunchBrowser::printErrors($fail);
		return(PUWI_LaunchBrowser::getClassAndNameTest($fail));
	}

	function getTestsError(PHPUnit_Framework_TestResult $result){
		return(PUWI_LaunchBrowser::getClassAndNameTest($result->errors()));
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

	function printErrors(array $fail){
		echo "\n..........PRINT........\n";
		foreach ($fail as $f){
			$data=PHPUnit_Util_Filter::getFilteredStacktrace(
			    $f->thrownException()
			);
			
			$file=strstr($data, ':', true);
			$line=substr(strstr($data, ':'),1);

		
			$file_to_open = fopen ($file, "r");
			$text = "";
			$number_line=1;
			while ($aux = fgets($file_to_open, 1024)){
				if (($line-2<=$number_line) && ($number_line<=$line+2)){ 
					$text .= $aux; 
				}
				$number_line++;
			}
			echo $text;
				
		}
	}
	
	function prueba(){
		echo "\n...FUNCIONAAAAAAAAAA\n";

	}
}
?>
