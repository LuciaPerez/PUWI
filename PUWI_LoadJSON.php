<?php	
	include 'PUWI_Command.php';
	
	$runner = new PUWI_Command();
	
	$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Command.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/");

	$results = $runner->run($argv,FALSE);
	
	$array = array('passed' => $results['passed'],'failures' => $results['failures'],'errors' => $results['errors'],
			'incomplete' => $results['incomplete'],'skipped' => $results['skipped'],'infoFailedTests' => $results['failedTests']);
	

	echo  json_encode($array);

	
?>

