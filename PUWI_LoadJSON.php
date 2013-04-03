<?php	
	include 'PUWI_Command.php';
	
	$runner = new PUWI_Command();
	
	$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Runner.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/","no_new");

	$results = $runner->run($argv,FALSE);

	$array = array('results' => $results);
	

	echo  json_encode($array);

?>

