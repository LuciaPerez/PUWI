<?php
include_once 'PUWI_UtilFilter.php';
class PUWI_LaunchBrowser{

	private $infoFailedTests = array(); 


	/*
	*@param integer $totalTests
	*@param string  $projectName
	*/

	public function launchBrowser($totalTests,$projectName,$passed,$failures,$errors,$incomplete,$skipped,$groups,$folders,$argv){
		print "-------------------FAILURES---------------------------<br/>";
		print_r($failures);
		
		$passed = $this->send_array($passed); 
		$failures = $this->send_array($failures);
		$errors = $this->send_array($errors);
		$incomplete = $this->send_array($incomplete);
		$skipped = $this->send_array($skipped);
		$groups = $this->send_array($groups);	
		$folders = $this->send_array($folders);
		$infoFailedTests = $this->send_array($this->infoFailedTests);
		$argv = $this->send_array($argv);
		
		print "<br/>==============FAILED TESTS===================<br/>";
		print_r($this->infoFailedTests);
		
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
		
		$groups_details = $result->topTestSuite()->getGroupDetails();
		$groups = $result->topTestSuite()->getGroups();
		$groups = $this->getGroups($groups_details,$groups);

		//echo "\n----------GROUPS-------------\n";
		//print_r($groups);
		
		if ($new == TRUE){
			$this->launchBrowser($totalTests,$projectName,$passed,$failures,$errors,$incomplete,$skipped,$groups,$folders,$argv);
		}else{
			return array("argv" => $argv,
						"projectName" => $projectName,
						 "totalTests" => $totalTests,
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

	function getGroups($groups_details,$groups){
	
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
		$this->getFails($fail,false);
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

	function getFails(array $fail, $singleTest){
		$infoEachTest = array();

		foreach ($fail as $f){
			$data = PUWI_UtilFilter::getFilteredStacktrace(
			    $f->thrownException()
			);
			
			$testName = $f->failedTest()->toString();
			$message = $f->getExceptionAsString();			
	
			$file=strstr($data, ':', true);
			$line=trim(substr(strstr($data, ':'),1));

			$infoEachTest['testName'] = $testName;
			
			$infoEachTest['file'] = $file;
			$infoEachTest['line'] = $line;
			$infoEachTest['message'] = $message;
			$infoEachTest['code'] = $this->getCode($file,$testName,$line);
			$infoEachTest['trace'] = (string)$f->thrownException();
			
			if ($singleTest == false){
				array_push($this->infoFailedTests,$infoEachTest);
			}else{
				return array($infoEachTest);
			}
		}
	}
	
	function getCode($file,$test,$line){
		$file_to_open = fopen ($file, "r");
		$testName = substr(strstr($test, ':'),2);
		$code = "";
		$number_line=1;
		$search = "/.".$testName."./";
		$in_function='no';
	
		$end_function = "/. function ./";
		$end_function2 = "/.\/\\*\\*./";
		
		while ($aux = fgets($file_to_open, 1024)){
			if (preg_match($search,$aux)){	
				$in_function='yes';
			}else{
				if ((preg_match($end_function,$aux)) || (preg_match($end_function2,$aux))){
					$in_function='no';
				}
			}
			if ($in_function=='yes'){
				if ($number_line == $line){
					$code .= '<span class="red">'.$aux.'</span></br>';
				}else{
					$code .= $aux."</br>";
				}
			}
			$number_line++;
		}
		$in_function='no';
		fclose($file_to_open);
		
		return $code;
	
	}

}
?>
