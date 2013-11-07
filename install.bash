#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
phpDir=`pear config-show 2> /dev/null | grep php_dir | awk '{print $NF}'`

. $scriptDir/bash/functions.bash
. $scriptDir/bash/messages.bash


function main {

	echo -e "\nLoading configuration...\n"
	cp $scriptDir/config.ini.inc $scriptDir/config.ini
	end_with_ok 

	read_config
	parseConfig

	addPathAutoload
	$scriptDir/load_deps.bash 

	mv $scriptDir/ $pubDirectory

	if [[ `echo ${pubDirectory: -1}` = "/" ]]
	then
		path_puwi=$pubDirectory'PUWI/'
	else
		path_puwi=$pubDirectory'/PUWI/'
	fi

	chmod 777 $pubDirectory/PUWI/	

	addIncludePath

	createAlias
	echo -e "\nAfter RESTART your terminal, you can use <puwi> command in your PHP projects!\n"
}

function parseConfig {
	theCommand='include_path=".:vendor/phpunit/phpunit/'
}

function addPathAutoload {
	pathAutoload="pathAutoload = vendor/phpunit/phpunit/PHPUnit/Autoload.php";
	echo $pathAutoload >> ./config.ini 
}

function addIncludePath {
	phpIniLocation=`find $serverDirectory -name php.ini 2>/dev/null`
	if [[ `grep "^include_path=\".:vendor/phpunit/phpunit" $phpIniLocation` = "" ]]
	then
		echo $theCommand:$phpDir"\"" >> $phpIniLocation
		echo -e "Restarting server to update php.ini configuration...\n"
		$runService restart
	fi
}

function createAlias {	
	if [[ `grep "^alias puwi=" ~/.bashrc` = "" ]]
	then
		echo 'alias puwi='$path_puwi'launch_project.bash' >> ~/.bashrc && . ~/.bashrc 
	fi
}

main $@



