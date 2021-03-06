<?php
include_once 'PUWI_UtilFilter.php';
include_once 'PUWI_TestSuite.php';

class PUWI_GetResults{
	
	/**
	 * @var array
	 */
	private $infoFailedTests = array(); 
	private $folder='';
	private $arrayFolders = array();
	private $pathProject='';

	/**
	 * Get all data about tests executed
	 * 
	 * @param string $projectName
	 * @param PHPUnit_Framework_TestResult $result
	 * @param array $argv
	 * @param PHPUnit_Framework_Test $suite
	 * @return array
	 */
	public function getResults($pathProject,$result,$argv,$suite,$coverage_path){
		$this->pathProject = $argv[1];
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
		
		$puwi_path = substr($argv[0],0,strripos($argv[0],"/"));

		$activate_coverage = '';
		if($coverage_path != ''){
			
			$puwi_path .= "/tmp/";
			if(is_dir($puwi_path)){
				$this->deleteTemporaryDirectory($puwi_path);
			}
			mkdir($puwi_path);
			
			$this->checkPhpunitConfiguration($coverage_path,$puwi_path.$projectName);
			$directories = (explode("/",$argv[1]));
			$activate_coverage = "http://localhost/PUWI/tmp/".$projectName;
			$continue = true;
			foreach($directories as $directory){
				if ($directory != $projectName && $continue){
					$activate_coverage .= $directory."/";
				}else{
					$continue = false;
				}
			}
			$activate_coverage .= "index.html";
		}
		return array("projectName" => $projectName,
					 "passed" => $passed,
					 "failures"=> $failures,
					 "errors" => $errors,
					 "incomplete" => $incomplete,
					 "skipped" => $skipped,
					 "groups" => $groups,
					 "folders" => $this->arrayFolders,
					 "failedTests" => $this->infoFailedTests,
					 "coverage" => $activate_coverage);
	}
	
	/**
	 * Delete temporary directory ('tmp/')
	 * 
	 * @param string $dir
	 */
	protected function deleteTemporaryDirectory($dir){
		if (is_dir($dir)) {
	     $objects = scandir($dir);
	     foreach ($objects as $object) {
	       if ($object != "." && $object != "..") {
	         if (filetype($dir."/".$object) == "dir"){
	         	$this->deleteTemporaryDirectory($dir."/".$object);
	         }else {unlink($dir."/".$object);}
	       }
	     }
	     reset($objects);
	     rmdir($dir);
	   }
	}
	
	
	/**
	 * Copy coverage-html files generated from the directory defined by the user to the server 
	 * 
	 * @param string $coverage_path
	 * @param string $puwi_path
	 */
	protected function checkPhpunitConfiguration($coverage_path,$puwi_path){
		$coverage_path = (substr($coverage_path,-1,1) != '/') ? $coverage_path.'/' : $coverage_path;
		$puwi_path = (substr($puwi_path,-1,1) != '/') ? $puwi_path.'/' : $puwi_path;

		if(!is_dir($puwi_path)){
			mkdir($puwi_path);
		}
		
		if (is_dir($coverage_path)) {
			if ($dir = opendir($coverage_path)) {
				
				while (false !== ($fileName = readdir($dir))) {
					if(is_file($coverage_path.$fileName) && $fileName!="." && $fileName!=".."){
						$file_to_open = fopen ($coverage_path.$fileName, "r");
						$file_to_copy = fopen($puwi_path.$fileName, "w");
						
						while ($content = fgets($file_to_open, 1024)){
							fwrite($file_to_copy, $content);
						}
						
						fclose($file_to_copy);
						fclose($file_to_open);
					}else{
						if(is_dir($coverage_path.$fileName) && $fileName!="." && $fileName!=".."){
							$this->checkPhpunitConfiguration($coverage_path.$fileName,$puwi_path.$fileName);
						}
					}
				}
					
				closedir($dir);
			}		
		}

	}
	
	/**
	 * Get directories from a given path
	 * 
	 * @param string $pathDir
	 */
	public function getArrayFolders($pathDir){
		$this->pathProject = $pathDir;
		$this->getFoldersProject($pathDir);
		return $this->arrayFolders;
	}
	
	/**
	 * Get only the directory name from a given path as of the path of main project
	 * 
	 * @param string $pathDir
	 */
	protected function getFolderName($pathDir){
		return substr($pathDir,strlen($this->pathProject));
	
	}
	
	/**
	 * Search subdirectories from a given path path 
	 * 
	 * @param string $pathDir
	 */
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
							$file_data[$file] = $this->find_classname($pathDir.$file);
							array_push($arrayFiles,$file_data);
	
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
	 * Find every class defined on a file
	 * 
	 * @param string $path_file
	 */
	
	protected function find_classname($path_file){
		$file_to_open = fopen ($path_file, "r");
		
		$class_name = array();
		$pattern = "/(*ANY)class /";
		
		while ($line = fgets($file_to_open, 1024)){
			if (preg_match($pattern,$line)){
				$line_class_name = preg_split($pattern,$line);
				$line_class_name = explode(' ',$line_class_name[1]);
				$line_class_name = explode('{',$line_class_name[0]);
				array_push($class_name,$line_class_name[0]);
			}
		}

		fclose($file_to_open);
		return $class_name;
	}
	
	/**
	 * Get groups of tests
	 * 
	 * @param array $groups_details
	 * @param array $groups
	 * @return array
	 */
	function getGroups($groups_details,$groups){
		$arrayResult = array();
		
		while (list($group_name, $group_content) = each($groups_details)){
			$arrayTests = array();
			while (list($index, $suite) = each($group_content)){				
				$suite = new PUWI_TestSuite($suite);
				$groups_suite = $suite->getGroupDetails();
				
				while (list($key, $value) = each($groups_suite[$group_name])){
					
					$class = get_class($value);
					if ($class == "PHPUnit_Framework_TestSuite_DataProvider"){
						array_push($arrayTests,$value->getName());
					}else{
						array_push($arrayTests,$class."::".$value->getName());
					}
				}
				
			}
			$arrayResult[$group_name] = $arrayTests;
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
		$this->getFails($fail);
		return $this->getClassAndNameTest($fail);
	}
	
	/**
	 * Get tests error
	 * 
	 * @param PHPUnit_Framework_TestResult $result
	 * @return array
	 */
	function getTestsError(PHPUnit_Framework_TestResult $result){
		$error=$result->errors();
		$this->getFails($error);
		return($this->getClassAndNameTest($error));
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
	function getFails(array $fail){
		$infoEachTest = array();

		foreach ($fail as $f){
			$data = PUWI_UtilFilter::getFilteredStacktrace(
			    $f->thrownException()
			);
			
			$testName = $f->failedTest()->toString();
			$message = $f->getExceptionAsString();			
			
			$data = explode("\n",$data);

			$array_aux = array ("");
			$data = array_values(array_diff($data,$array_aux));
			$data = array_pop($data);

			$file=strstr($data, ':', true);
			$line=trim(substr(strstr($data, ':'),1));


			$infoEachTest['testName'] = $testName;
			$infoEachTest['file'] = $file;
			$infoEachTest['line'] = $line;
			$infoEachTest['message'] = $message;
			$infoEachTest['code'] = $this->getCode($file,$testName,$line);
			$infoEachTest['trace'] = (string)$f->thrownException();
				
			array_push($this->infoFailedTests,$infoEachTest);

		}
	}
	
	public function getInfoFailedTests(){
		return $this->infoFailedTests;
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
		$testName = explode(' ',$test);
		$testName = substr(strstr($testName[0], ':'),2);

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
