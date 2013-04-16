<?php	
	include_once 'PUWI_Command.php';

	$action = $_POST['action'];
	switch($action)
	{
		case 'rerun':
			$runner = new PUWI_Command;
			$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/");
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

