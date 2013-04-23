<?php	
	include_once 'PUWI_Command.php';
	$action = $_POST['action'];
	//$action = 'runFile';
	switch($action)
	{
		case 'rerun':
			$URLParams = $_POST['argv'];
			$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
			$URLParams[1] = $URLParams[1]."/";
			
			$runner = new PUWI_Command;
			$argv=array($URLParams[0],$URLParams[1]);
			$results = $runner->run($argv,FALSE);
			
	
			$array = array('projectName' => $results['projectName'], 'totalTests' =>$results['totalTests'], 'passed' => $results['passed'],
					'failures' => $results['failures'],'errors' => $results['errors'], 'incomplete' => $results['incomplete'],
					'skipped' => $results['skipped'], 'groups' => $results['groups'], 'folders' => $results['folders'],
					'infoFailedTests' => $results['failedTests']);

			echo  json_encode($array);
		break;
		
		case 'runFolder':
			$URLParams = $_POST['argv'];
			$folderName = $_POST['folderName'];
			$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
			$URLParams[1] = $URLParams[1]."/".$folderName."/";
				
			$runner = new PUWI_Command;
			$argv=array($URLParams[0],$URLParams[1]);
			$results = $runner->run($argv,FALSE);
			
			$array = array('projectName' => $results['projectName'], 'totalTests' =>$results['totalTests'], 'passed' => $results['passed'],
					'failures' => $results['failures'],'errors' => $results['errors'], 'incomplete' => $results['incomplete'],
					'skipped' => $results['skipped'], 'groups' => $results['groups'], 'folders' => $results['folders'],
					'infoFailedTests' => $results['failedTests']);
			
			echo  json_encode($array);
		break;
		
		case 'displayCode':
			$file = $_POST['file'];
			$line = $_POST['line'];
			$testName = $_POST['testName'];
			
			$code = getCode($file,$testName,$line);
			$array = array ('code' => $code);
			echo json_encode($array);
		break;
		
		case 'runTest':
			$results = runCommand();
			if ($results['result'] == "testOK" || $results['result'] == "testIncomplete"){
				$array = array ('result' => $results['result']);
			}else{
				$array = array ('result' => $results['result'],'testName' => $results['testName'],'file' => $results['file'],
						    	'line' => $results['line'],'message' => $results['message'],'trace' => $results['trace']);
			}

			echo json_encode($array);
		break;
		
		case 'runFile':
			$results = runCommand();

			$array = array('result' => $results);
			echo json_encode($array);
		break;
		
	}
	
	function runCommand(){
		$elementName = $_POST['name'];
		$type = $_POST['type'];
		$URLParams = $_POST['argv'];
		$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
		$URLParams[1] = $URLParams[1]."/";
		
		$runner = new PUWI_Command;
		$argv=array($URLParams[0],$URLParams[1],$elementName,$type);
		//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/","AddTest::test_setUpWorks");
		return $runner->run($argv,FALSE);
	}
	
	function getCode($file,$test,$line){
		$file_to_open = fopen ($file, "r");
		$code = "";
		$number_line=1;
		$search = "/.".$test."./";
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
	
?>

