<?php
require('puwi/setup.php');

class index{
	private $showedClass='';
	private $showedFolder='';
	
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

	function getClassNameTest($value, $passed, $incomplete, $skipped, $errors){
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
	}
	
	function is_showedFolder($folder){
		if($this->showedFolder == $folder){
			return 'no';
		}else{
			$this->showedFolder=$folder;
			return 'yes';
		}
	}
	
	function getFolder ($input,$input_class){
		$keys_input = array_keys($input);
		$result='';
		foreach ($keys_input as $folder){
			$values = array_values($input[$folder]);
			foreach ($values as $file){
				$class = strstr($file, '.', true);
				if ($class == $input_class){
					$result = $folder;
				}
			}
		}
		return $result;
	}
	
	function getInfoFailedTests($testName, $infoFailedTests){
		foreach($infoFailedTests as $failedTest){
			if ($failedTest["testName"] == $testName){
				return $failedTest;
			}

		}
		
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
		$folders = $index->get_URLData('folders');
		$infoFailedTests = $index->get_URLData('infoFailedTests');
		
		$smarty = new Smarty_Puwi();

		$smarty->display("header.tpl");
		$smarty->display("results.tpl");
		
		$keys_groups = array_keys($groups);
		$count = 0;
		foreach ($keys_groups as $group){
			$smarty->assign("group",$group);
			
			$values = array_values($groups[$group]);
			foreach($values as $value){
				$smarty->assign("count",$count);
				
				$class=strstr($value, ':', true);
				$test=substr(strrchr($value, ":"), 1);
				$classNameTest = $index->getClassNameTest($value, $passed, $incomplete, $skipped, $errors);
				
				if ($classNameTest == 'testFailed box'){
					$infoFail = $index->getInfoFailedTests($value,$infoFailedTests);
					$file=strstr($infoFail["data"], ':', true);
					$line=trim(substr(strstr($infoFail["data"], ':'),1));
					$message = trim($infoFail["message"]);
					$smarty->assign(array("file" => $file, "line" => $line, "message" => $message));


					$file_to_open = fopen ($file, "r");
					$code = "";
					$number_line=1;
					$search = "/.".$test."./";
					$in_function='no';
					$end_function = "/.test_setUpJob./";
					
					while ($aux = fgets($file_to_open, 1024)){
						if (preg_match($search,$aux)){ 
							$in_function='yes';
						}else{
							if (preg_match($end_function,$aux)){		
								$in_function='no';
							}
						}
						if ($in_function=='yes'){
							$code .= $aux."--";
						}
						$number_line++;
					}
					$in_function='no';
					fclose($file_to_open);
					print ">--".trim($code)."--<";
					$text = ') {$this->fail("Here an error!!!");';
					
					$smarty->assign("code",trim($text));

				}

				$folder = $index->getFolder($folders,$class);
				
				$createClassNameDiv = $index->is_showedClass($class);
				$createFolderDiv = $index->is_showedFolder($folder);
				
				$smarty->assign(array('createClassNameDiv' => $createClassNameDiv, 
						      'createFolderDiv' => $createFolderDiv,
						      'class' => $class,
						      'classNameTest' => $classNameTest,
						      'test' => $test,
						      'folder' => $folder));
				
				
				$smarty->display("tests.tpl");
				
				$smarty->clear_assign(array('group','class','classNameTest', 'test','folder','file','line','message','code'));
				$smarty->clear_cache('tests.tpl');
				$count++; 	
			}//end foreach values
			
			$index->showedClass = '';
			$index->showedFolder = '';
			$smarty->clear_assign(array('group'));
			$smarty->clear_cache('tests.tpl');

		}//end foreach groups

		$smarty->display("footer.tpl");
		$smarty->clear_all_assign();
		$smarty->clear_all_cache();
	}

}

index::main();




?>

