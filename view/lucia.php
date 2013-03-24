<?php
	include 'prueba.php';
	
	$pr = new prueba();
	
	$operador = $_POST['parametro'];
	
	echo $pr->getprueba();

	//exec("php prueba.php hola lucia");


	header("Status: 200 OK", true, 200); 

	echo $operador;
	exit(0);
?>

