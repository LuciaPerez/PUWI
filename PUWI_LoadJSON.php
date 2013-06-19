<?php	
	include_once 'PUWI_Command.php';
	$action = $_POST['action'];
	//$action = 'rerun';
	
	function selectRunner($action){
		switch($action)
		{
			case 'rerun':
				$URLParams = $_POST['argv'];
				$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
				$URLParams[1] = $URLParams[1]."/";
				
				$runner = createCommand();
				$argv=array($URLParams[0],$URLParams[1]);
				//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/home/lucia/Calculadora/");
				$results = $runner->run($argv);
	
				sendData($results);
			break;
			
			case 'runFolder':
				$URLParams = $_POST['argv'];
				$folderName = $_POST['folderName'];
				$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
				$URLParams[1] = $URLParams[1]."/".$folderName;
					
				$runner = createCommand();
				$argv=array($URLParams[0],$URLParams[1]);
				//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/testsRepes/");
				$results = $runner->run($argv);
				sendData($results);
			break;
			
			case 'runTests':
				$elementName = $_POST['name'];
				$type = $_POST['type'];
				$URLParams = $_POST['argv'];
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
		//print_r($array);
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
		//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/home/lucia/Calculadora/","AddTest","file");
		//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/home/lucia/Calculadora/","grupo1","group");
		//$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/home/lucia/Calculadora/","AddTest::test_setUpWorksAdd","test");
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
	
	
	selectRunner($action);
	
?>

