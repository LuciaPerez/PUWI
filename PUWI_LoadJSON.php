<?php	
	include_once 'PUWI_Command.php';
	$action = $_POST['action'];
	switch($action)
	{
		case 'rerun':
			$URLParams = $_POST['argv'];
			$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
			$URLParams[1] = $URLParams[1]."/";
			
			$runner = new PUWI_Command;
			$argv=array($URLParams[0],$URLParams[1]);
			$results = $runner->run($argv,FALSE);

			sendData($results);
		break;
		
		case 'runFolder':
			$URLParams = $_POST['argv'];
			$folderName = $_POST['folderName'];
			$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
			$URLParams[1] = $URLParams[1]."/".$folderName;
				
			$runner = new PUWI_Command;
			$argv=array($URLParams[0],$URLParams[1]);
			$results = $runner->run($argv,FALSE);
			sendData($results);
		break;
		
		case 'runTests':
			$results = runCommand();
			sendData($results);
		break;
		
	}
	
	function sendData($results){
		$array = array('result' => $results);
		echo json_encode($array);
	}
	
	function runCommand(){
		$elementName = $_POST['name'];
		$type = $_POST['type'];
		$URLParams = $_POST['argv'];
		$URLParams[0] = $URLParams[0]."/PUWI_Command.php";
		$URLParams[1] = $URLParams[1]."/";
		
		$runner = new PUWI_Command;
		$argv=array($URLParams[0],$URLParams[1],$elementName,$type);
		return $runner->run($argv,FALSE);
	}
	
?>

