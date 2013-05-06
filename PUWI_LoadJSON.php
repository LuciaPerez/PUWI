<?php	
	include_once 'PUWI_Command.php';
	$action = $_POST['action'];
	//$action = 'rerun';
	switch($action)
	{
		case 'rerun':
			$URLParams = $_POST['argv'];
			$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
			$URLParams[1] = $URLParams[1]."/";
			
			$runner = new PUWI_Command;
			$argv=array($URLParams[0],$URLParams[1]);
			//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/testsRepes/");
			$results = $runner->run($argv,FALSE);
			
	
			$array = array('projectName' => $results['projectName'], 'totalTests' =>$results['totalTests'], 'passed' => $results['passed'],
					'failures' => $results['failures'],'errors' => $results['errors'], 'incomplete' => $results['incomplete'],
					'skipped' => $results['skipped'], 'groups' => $results['groups'], 'folders' => $results['folders'],
					'infoFailedTests' => $results['failedTests']);
			//print_r($array);
			echo  json_encode($array);
		break;
		
		case 'runFolder':
			$URLParams = $_POST['argv'];
			$folderName = $_POST['folderName'];
			$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
			$URLParams[1] = $URLParams[1]."/".$folderName;
				
			$runner = new PUWI_Command;
			$argv=array($URLParams[0],$URLParams[1]);
			//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/testsRepes/");
			$results = $runner->run($argv,FALSE);

			$array = array('result' => $results);
			//print_r($array);

			echo  json_encode($array);
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
		//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/","grupo3","group");
		return $runner->run($argv,FALSE);
	}
	
?>

