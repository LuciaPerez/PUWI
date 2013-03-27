<?php	

	//$operador = $_POST['parametro'];
	
	$who = " OWNER: ".exec ('whoami');
	
	$res1= exec("../run2.bash");
	

	$array = array('probando' => $res1, 'whoami' => $who);
	echo  json_encode($array);

	
	header("Status: 200 OK", true, 200); 

	exit(0);
?>

