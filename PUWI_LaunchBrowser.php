<?php

class PUWI_LaunchBrowser{


	public function getProjectName($projectName){
		$names=preg_split("/[\/]tests/",$projectName);
		$projectName=explode("/",$names[0]);
		$size=sizeof($projectName);
		return $projectName[$size-1];
	}

	/*
	*@param integer $totalTests
	*@param string  $projectName
	*/

	public function launchBrowser($totalTests,$projectName){
	
		$projectName=PUWI_LaunchBrowser::getProjectName($projectName);
		echo $projectName;

		$url="http://localhost/view/puwi.php"."?projectName=".$projectName."\&totalTests=".$totalTests;
		$command="x-www-browser ".$url." &";
		system($command);

	}


}

?>
