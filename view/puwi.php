<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
	<meta  http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<link rel="icon" type="image/png" href="images/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<script type="text/javascript" src="./js/scripts.js" ></script>

	<title>PhpUnit Web Interface</title>
</head>

<body id="background">


<div id="container">
	<div id="header">
	<img src="images/cabecera.png" alt="Cabecera" width="1000" height="170"/>
	</div>
	<div id="content">
		<form> 
		<input type="button" onclick="createDiv(this)" value="divsDinamicos"/> 
		</form> 
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

		<div class="classTest">
		<p>Boot</p>
		</div>
	
		<div class="testOK box">
		<p>onCreate_runsApp</p>
		</div>

		<div class="classTest">
		<p>Translator</p>
		</div>
	
		<div class="testOK box">
		<p>canTranslateAnURL</p>
		</div>

		<div class="testOK box">
		<p>canTranslateAString</p>
		</div>

		<div class="testFailed box">
		<p>canTranslateFromOtherLanguage</p>
		</div>
	</div>

	<div id="footer">
		<p> PUWI - PhpUnit Web Interface </p>
	</div>
</div>

</body>
</html>