#!/bin/bash
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
phpDir=`pear config-show 2> /dev/null | grep php_dir | awk '{print $NF}'`

function main {
	parseConfig
	$theCommand:$phpDir $scriptDir/PUWI_Runner.php $*
}

function parseConfig {
	theCommand='php -d include_path=.'
	while read line
	do 
		checkEnterSection && checkEnterDeps
		checkVarInLine "$line"
		[ "$isDep" == "yes" ] && theCommand=$theCommand:$( getWord "$line" 3 )
	done < $scriptDir/config.ini
}

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

main $@
