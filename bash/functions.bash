#!/bin/bash


function checkEnterSection {
	[[ "$line" =~ \[.*\] ]] && inDeps="no"
}

inDeps="no"

function checkEnterDeps {
	[ "$line" == "[dependencies]" ] && inDeps="yes"
}

function checkVarInLine {
	sign=$( getWord "$1" 2 )
	[ "$sign" != "" ] && [ "$inDeps" == "yes" ] && isDep="yes" || isDep="no"
}

isDep="no"

function getWord {
	echo $1 | awk '{print $'$2'}'
}

function change_owner {
	sudo mkdir tmp_puwi/
	
	echo $1 | sudo tee -a tmp_puwi/tmp.txt 1>/dev/null

	pubDirectory=`cut -d "/" -f4 tmp_puwi/tmp.txt`

	
	param=$1
	length_dir=`expr length $pubDirectory`
	total_length=`expr length $param`
	
	if [[ `echo ${param: -1}` = "/" ]]
	then
		total_length=`expr $total_length - 1`
		param=`expr substr $param 1 $total_length`
	fi

	length_path_pub_dir=`expr $total_length - $length_dir`

	path_pub_dir=`expr substr $param 1 $length_path_pub_dir`
	
	ls -l $path_pub_dir | sudo tee -a tmp_puwi/permissions.txt 1>/dev/null 

	user=`awk '/'$pubDirectory'/ {print $0}' tmp_puwi/permissions.txt | cut -d " " -f4`

	group=`awk '/'$pubDirectory'/ {print $0}' tmp_puwi/permissions.txt | cut -d " " -f5`


	sudo chown $user $2
	sudo chgrp $group $2
	
	sudo rm tmp_puwi/tmp.txt tmp_puwi/permissions.txt
	sudo rmdir tmp_puwi/
}

function read_config {
	while read line
	do 
		checkEnterSection && checkEnterDeps
		path=`echo $line | awk '{print $3}'`
		
		key=`echo $line | awk '{print $1}'`
		searchServerInformation $key $path
	done < $scriptDir/config.ini

}

serverDirectory=""
pubDirectory=""
runService=""
function searchServerInformation {
	[ "$1" == "serverDirectory" ] && serverDirectory=$2
	[ "$1" == "pubDirectory" ] && pubDirectory=$2
	[ "$1" == "runService" ] && runService=$2
}
