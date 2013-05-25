#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
phpDir=`pear config-show 2> /dev/null | grep php_dir | awk '{print $NF}'`

. $scriptDir/functions.bash
. $scriptDir/bash/messages.bash


function main {

	echo -e "\nLoading configuration...\n"
	cp $scriptDir/config.ini.inc $scriptDir/config.ini
	end_with_ok 

	read_config

	addPathAutoload
	$scriptDir/load_deps.bash 

	cp -r $scriptDir/. $pubDirectory

	echo $theCommand:$phpDir"\"" >> `find $serverDirectory -name php.ini 2>/dev/null` 
	echo -e "Restarting server to update php.ini configuration...\n"
	$runService restart

	createAlias
}

function read_config {
	regex="^.*\/vendor\/.*"
	
	while read line
	do 
		checkEnterSection && checkEnterDeps
		path=`echo $line | awk '{print $3}'`
		
		key=`echo $line | awk '{print $1}'`
		searchServerInformation $key $path
	done < $scriptDir/config.ini
	
	parseConfig
}

serverDirectory=""
pubDirectory=""
runService=""
function searchServerInformation {
	[ "$1" == "serverDirectory" ] && serverDirectory=$2
	[ "$1" == "pubDirectory" ] && pubDirectory=$2"PUWI/"
	[ "$1" == "runService" ] && runService=$2
}

function parseConfig {
	theCommand='include_path=".'
	while read line
	do 
		checkEnterSection && checkEnterDeps
		checkVarInLine "$line"
		[ "$isDep" == "yes" ] && theCommand=$theCommand:$pubDirectory$( getWord "$line" 3 )	
	done < $scriptDir/config.ini
}

function addPathAutoload {
	pathAutoload="pathAutoload = "$pubDirectory"vendor/phpunit/phpunit/PHPUnit/Autoload.php";
	echo $pathAutoload >> ./config.ini 
}

function createAlias {	
	if [[ `grep "^alias puwi=" ~/.bashrc` = "" ]]
	then
		echo 'alias puwi='$scriptDir'/launch_project.bash' >> ~/.bashrc && . ~/.bashrc 
	fi
}

main $@



