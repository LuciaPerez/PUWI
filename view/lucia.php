<?php	
	include_once "../PUWI_LaunchBrowser.php";
	include "../PUWI_Runner.php";
	
	
	$operador = $_POST['parametro'];

	//exec("bash prueba.bash");


	switch($_POST["action"]){

		case 'getCode2':
			$pr = new PUWI_Runner();
			echo $pr->aux("probando <-----");
			break;
	}
	
	
	header("Status: 200 OK", true, 200); 

	echo $operador;
	exit(0);
?>

