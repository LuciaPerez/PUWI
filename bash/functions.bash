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
