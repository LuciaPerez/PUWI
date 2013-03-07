<?php

class PUWI_LaunchBrowser{

	/*
	*@param integer $totalTests
	*@param string  $projectName
	*/

	public function launchBrowser($totalTests,$projectName,$passed,$failures){
		$passed=PUWI_LaunchBrowser::send_array($passed); 
		$failures=PUWI_LaunchBrowser::send_array($failures);

		$projectName=PUWI_LaunchBrowser::getProjectName($projectName);

		$url="http://localhost/view/puwi.php"."?projectName=".$projectName."\&totalTests=".$totalTests."\&passed=".$passed."\&failures=".$failures;

		$command="x-www-browser ".$url." &";
		system($command);

	}


	public function getProjectName($projectName){
		$names=preg_split("/[\/]tests/",$projectName);
		$projectName=explode("/",$names[0]);
		$size=sizeof($projectName);
		return $projectName[$size-1];
	}


	function send_array($array) { 
	    $tmp = serialize($array); 
	    $tmp = urlencode($tmp); 
	    return $tmp; 
	} 

}
?>
