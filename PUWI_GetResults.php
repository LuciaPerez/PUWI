<?php
include_once 'PUWI_UtilFilter.php';
class PUWI_GetResults{
	
	/**
	 * @var array
	 */
	private $infoFailedTests = array(); 
	private $folder='';
	private $arrayFolders = array();

	/**
	 * Get all data about tests executed
	 * 
	 * @param string $projectName
	 * @param PHPUnit_Framework_TestResult $result
	 * @param array $argv
	 * @param PHPUnit_Framework_Test
	 * @return array
	 */
	public function getResults($pathProject,$result,$argv,$suite){
		define("PATH_PROJECT", $argv[1]);
		
		$totalTests = $result->count();
		$projectName = $this->getProjectName($pathProject);
	
		$passed = $this->getTestsPassed($result);
		$failures = $this->getTestsFailed($result);
		$errors = $this->getTestsError($result);
		$incomplete = $this->getTestsIncompleted($result);
		$skipped = $this->getTestsSkipped($result);
		
		$this->getFoldersProject($argv[1]);

		$groups_details = $suite->getGroupDetails();
		$groups = $suite->getGroups();
		$groups = $this->getGroups($groups_details,$groups);
		

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
					 "folders" => $this->arrayFolders,
					 "failedTests" => $this->infoFailedTests);

	}
	
	public function getArrayFolders($pathDir){
		define("PATH_PROJECT", $pathDir);
		$this->getFoldersProject($pathDir);
		return $this->arrayFolders;
	}
	
	protected function getFolderName($pathDir){
		return substr($pathDir,strlen(PATH_PROJECT));
	
	}
	
	public function getFoldersProject($pathDir){
		$regex="/^\./";
		
		if (is_dir($pathDir)) {
	
			$arrayFiles = array();
			if ($dir = opendir($pathDir)) {
				while (($file = readdir($dir)) !== false) {
					if (is_dir($pathDir . $file) && $file!="." && $file!=".." && !preg_match($regex,$file)){
						$this->folder= $pathDir . $file;
						$this->getFoldersProject($this->folder . "/");
					} else{
						if($file!="." && $file!=".." && $file!=".." && !preg_match($regex,$file)){
							array_push($arrayFiles,$file);
	
						}
						if (count($arrayFiles) != 0){
							$folderName = $this->getFolderName($pathDir);
							$this->arrayFolders[$folderName]=$arrayFiles;
							
						}
					}
	
				}
					
				closedir($dir);
			}
	
	
		}
	
	}
	
	/**
	 * Get groups of tests
	 * 
	 * @param array $groups_details
	 * @param array $groups
	 * @return array
	 */
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
		krsort($arrayResult);
		return $arrayResult;
	}

	/**
	 * Get project name from the full path
	 * 
	 * @param string $projectName
	 */
	public function getProjectName($projectName){
		$projectName=explode("/",$projectName);
		$size=sizeof($projectName);
		
		return $projectName[$size-2];
	}

	/**
	 * Get tests passed
	 * 
	 * @param PHPUnit_Framework_TestResult $result
	 * @return array $passed
	 */
	function getTestsPassed(PHPUnit_Framework_TestResult $result){
		$r=$result->passed();
		$passed=array_keys($r);
		return $passed;
	} 

	/**
	 * Get tests failed
	 * 
	 * @param PHPUnit_Framework_TestResult $result
	 * @return array
	 */
	function getTestsFailed(PHPUnit_Framework_TestResult $result){
		$fail=$result->failures();
		$this->getFails($fail,false);
		return $this->getClassAndNameTest($fail);
	}
	
	/**
	 * Get tests error
	 * 
	 * @param PHPUnit_Framework_TestResult $result
	 * @return array
	 */
	function getTestsError(PHPUnit_Framework_TestResult $result){
		return($this->getClassAndNameTest($result->errors()));
	}
	
	/**
	 * Get tests incomplete
	 *
	 * @param PHPUnit_Framework_TestResult $result
	 * @return array
	 */
	function getTestsIncompleted(PHPUnit_Framework_TestResult $result){
		return($this->getClassAndNameTest($result->notImplemented()));
	}

	/**
	 * Get tests skipped
	 *
	 * @param PHPUnit_Framework_TestResult $result
	 * @return array
	 */
	function getTestsSkipped(PHPUnit_Framework_TestResult $result){
		return($this->getClassAndNameTest($result->skipped()));
	}
	
	/**
	 * Get class and name belongs to every test
	 *
	 * @param array $tests
	 * @return array $result
	 */
	function getClassAndNameTest(array $tests){
		$result = array();
		foreach ($tests as $test){
			$t=$test->failedTest();
			$class=get_class($t);
			$name=$t->getName();
			$fullName=$class."::".$name;
			array_push($result,$fullName);
		}
		return $result;
	}

	/**
	 * Get information about a failed test
	 * 
	 * @param array $fail
	 * @param boolean $singleTest
	 * @return array $infoEachTest
	 */
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
				return $infoEachTest;
			}
		}
	}
	
	/**
	 * Search code of a test 
	 * 
	 * @param string $file
	 * @param string $test
	 * @param string $line
	 * @return string $code
	 */
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
