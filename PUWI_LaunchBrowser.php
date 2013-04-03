<?php

class PUWI_LaunchBrowser{

	private $infoFailedTests = array(); 


	/*
	*@param integer $totalTests
	*@param string  $projectName
	*/

	public function launchBrowser($totalTests,$projectName,$passed,$failures,$errors,$incomplete,$skipped,$groups,$folders,$argv){
				
		$passed = $this->send_array($passed); 
		$failures = $this->send_array($failures);
		$errors = $this->send_array($errors);
		$incomplete = $this->send_array($incomplete);
		$skipped = $this->send_array($skipped);
		$groups = $this->send_array($groups);	
		$folders = $this->send_array($folders);
		$infoFailedTests = $this->send_array($this->infoFailedTests);
		$argv = $this->send_array($argv);
		//print "==============FAILED TESTS===================<br/>";
		//print_r($this->infoFailedTests);
		$url="http://localhost/PUWI/view/index.php".
		     "?projectName=".$projectName.
		     "\&totalTests=".$totalTests.
		     "\&passed=".$passed.
		     "\&failures=".$failures.
		     "\&errors=".$errors.
		     "\&incomplete=".$incomplete.
		     "\&skipped=".$skipped.
		     "\&groups=".$groups.
		     "\&folders=".$folders.
		     "\&infoFailedTests=".$infoFailedTests.
		     "\&argv=".$argv;

		$command="x-www-browser ".$url." &";
		system($command);

	}

	public function getResults($projectName,$result,$folders,$new,$argv){
		$totalTests = $result->count();
		$projectName = $this->getProjectName($projectName);
	
		$passed = $this->getTestsPassed($result);
		$failures = $this->getTestsFailed($result);
		$errors = $this->getTestsError($result);
		$incomplete = $this->getTestsIncompleted($result);
		$skipped = $this->getTestsSkipped($result);
		$groups = $this->getGroups($result);

		//echo "\n----------GROUPS-------------\n";
		//print_r($groups);
		
		if ($new == TRUE){
			$this->launchBrowser($totalTests,$projectName,$passed,$failures,$errors,$incomplete,$skipped,$groups,$folders,$argv);
		}else{
			return array("totalTests" => $totalTests,
						 "projectName" => $projectName,
						 "passed" => $passed,
						 "failures"=> $failures,
						 "errors" => $errors,
						 "incomplete" => $incomplete,
						 "skipped" => $skipped,
						 "groups" => $groups,
						 "folders" => $folders,
						 "failedTests" => $this->infoFailedTests);
		}

	}

	function getGroups($result){
		$groups_details = $result->topTestSuite()->getGroupDetails();
		$groups = $result->topTestSuite()->getGroups();
		
		$index=0;
		$arrayResult = array();
		foreach ($groups_details as $group){
			$group_values = array_values($group);
			$arrayTests = array();
			foreach ($group_values as $g){
				$gd = $g->getGroupDetails();
				$array= $gd[$groups[$index]];
		
				foreach ($array as $test){
					array_push($arrayTests,$g->getName()."::".$test->getName());
				}
			
			}
			$arrayResult[$groups[$index]] = $arrayTests;
			$index++;
		}

		return $arrayResult;
	}

	function send_array($array) { 
	    $tmp = serialize($array); 
	    $tmp = urlencode($tmp); 
	    return $tmp; 
	}
	
	public function getProjectName($projectName){
		$projectName=explode("/",$projectName);
		$size=sizeof($projectName);
		
		return $projectName[$size-2];
	}

	function getTestsPassed(PHPUnit_Framework_TestResult $result){
		$r=$result->passed();
		$passed=array_keys($r);
		return($passed);
	} 

	function getTestsFailed(PHPUnit_Framework_TestResult $result){
		$fail=$result->failures();
		$this->getFails($fail);
		return($this->getClassAndNameTest($fail));
	}

	function getTestsError(PHPUnit_Framework_TestResult $result){
		return($this->getClassAndNameTest($result->errors()));
	}

	function getTestsIncompleted(PHPUnit_Framework_TestResult $result){
		return($this->getClassAndNameTest($result->notImplemented()));
	}

	function getTestsSkipped(PHPUnit_Framework_TestResult $result){
		return($this->getClassAndNameTest($result->skipped()));
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

	function getFails(array $fail){
		$infoEachTest = array();

		foreach ($fail as $f){
			$data=PHPUnit_Util_Filter::getFilteredStacktrace(
			    $f->thrownException()
			);
			
			$testName = $f->failedTest()->toString();
			$message = $f->getExceptionAsString();

			$infoEachTest['testName'] = $testName;
			
			$infoEachTest['data'] = $data;
			$infoEachTest['message'] = $message;

			array_push($this->infoFailedTests,$infoEachTest);
		}
	}

}
?>
