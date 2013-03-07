<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
	<meta  http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<link rel="icon" type="image/png" href="images/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/scripts.js" ></script>

	<title>PhpUnit Web Interface</title>
</head>

<body id="background">
<?php 

function receive_array($url_array) { 
    $tmp = stripslashes($url_array); 
    $tmp = urldecode($tmp); 
    $tmp = unserialize($tmp); 
    return $tmp; 
} 

function createDinamicDivs(){
	$passed=$_GET['passed']; 
	$passed=receive_array($passed); 

	$failures=$_GET['failures'];
	$failures=receive_array($failures);

	$totalTestsReceived=array_merge($passed,$failures);
	sort($totalTestsReceived);
	
	$showedClass="";

	foreach ($totalTestsReceived as $key => $value){  
		$class=strstr($value, ':', true);
		$test=substr(strrchr($value, ":"), 1);
		
		$className="classTest";	
		$contentDiv=$class;
		if($showedClass != $class){
	?>
		<script type="text/javascript">
		  createDiv("<?php echo $contentDiv; ?>","<?php echo $className; ?>");
		</script>
	<?php
		$showedClass=$class;
		}

		$contentDiv=$test;
		if(in_array($value,$passed)){
		$className="testOK box";} else {$className="testFailed box";}	
		
	?>
		<script type="text/javascript">
		  createDiv("<?php echo $contentDiv; ?>","<?php echo $className; ?>");
		</script>
	<?php
	} 

}
?>

<div id="container">

	<div id="header">
	<img src="images/cabecera.png" alt="Cabecera" width="1000" height="170"/>
	</div>
	<div id="content">

		<div id="projectName">
		<?
			$projectName=$_GET['projectName'];
			echo "<p>$projectName</p>";			
		?>
		</div>

		<div id="totalTests" class="box">
		<?
			$totalTests=$_GET['totalTests'];
			echo "<p>$totalTests test passing</p>";			
		?>
		</div>

		<?php createDinamicDivs();?>

	</div>
</div>
<div id="footer">
	<p> PUWI - PhpUnit Web Interface </p>
</div>
</body>
</html>
