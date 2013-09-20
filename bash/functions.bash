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
	mkdir tmp/
	echo $1 > tmp/tmp.txt
 	
	pubDirectory=`cut -d "/" -f4 tmp/tmp.txt`

	length_dir=`expr length $pubDirectory`
	total_length=`expr length $1`
	length_path_pub_dir=`expr $total_length - $length_dir`

	path_pub_dir=`expr substr $1 1 $length_path_pub_dir`

	ls -l $path_pub_dir > tmp/permissions.txt

	user=`awk '/'$pubDirectory'/ {print $0}' tmp/permissions.txt | cut -d " " -f4`

	group=`awk '/'$pubDirectory'/ {print $0}' tmp/permissions.txt | cut -d " " -f5`


	if [[ `expr substr $1 -1 1` = "/" ]]
	then
		path_puwi=$1"PUWI/"
	else 
		path_puwi=$1"/PUWI/"
	fi

	chown $user $path_puwi
	chgrp $group $path_puwi
	
	rm tmp/tmp.txt tmp/permissions.txt
	rmdir tmp/
}
