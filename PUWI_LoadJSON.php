<?php	
	include 'PUWI_Runner.php';
	
	$runner = new PUWI_Runner();
	
	$argv=array("/opt/lampp/htdocs/PUWI/PUWI_Runner.php","/opt/lampp/htdocs/workspace-eclipse/Calculadora/","no_new");

	$res = $runner->run($argv,FALSE);

	$who = array ("hola","adios");
	$array = array('whoami' => $who[0],'runner' => $res['projectName']);
	

	echo  json_encode($array);

?>

