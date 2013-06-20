<?php	
	include_once 'PUWI_Command.php';
	$action = $_POST['action'];
	$URLParams = $_POST['argv'];
	
	function selectRunner($action,$URLParams){
		switch($action)
		{
			case 'rerun':
				$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
				$URLParams[1] = $URLParams[1]."/";
				
				$runner = createCommand();
				$argv=array($URLParams[0],$URLParams[1]);
				$results = $runner->run($argv);
	
				sendData($results);
			break;
			
			case 'runFolder':
				$folderName = $_POST['folderName'];
				$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
				$URLParams[1] = $URLParams[1]."/".$folderName;
					
				$runner = createCommand();
				$argv=array($URLParams[0],$URLParams[1]);

				$results = $runner->run($argv);
				sendData($results);
			break;
			
			case 'runTests':
				$elementName = $_POST['name'];
				$type = $_POST['type'];
				$results = runCommand($elementName,$type,$URLParams);
				sendData($results);
			break;
			
		}
	}
	
	
	/**
	 * Encode data in JSON 
	 * 
	 * @param array $results
	 */
	function sendData($results){
		$array = array('result' => $results);
		echo json_encode($array);
	}
	
	/**
	 * Run group of tests (group, file, class)
	 * 
	 * @return array
	 */
	function runCommand($elementName,$type,$URLParams){
		
		$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
		$URLParams[1] = $URLParams[1]."/";
		
		$runner = createCommand();
		$argv=array($URLParams[0],$URLParams[1],$elementName,$type);
	
		return $runner->run($argv,FALSE);
	}
	
	/**
	 * Create a PUWI_Command
	 * 
	 * @return PUWI_Command
	 */
	function createCommand(){
		return new PUWI_Command;
	} 
	
	
	selectRunner($action,$URLParams);
	
?>

