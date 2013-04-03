#!/bin/bash
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
phpDir=`pear config-show 2> /dev/null | grep php_dir | awk '{print $NF}'`

. $scriptDir/functions.bash


function main {
	parseConfig
	$theCommand:$phpDir $scriptDir/PUWI_Command.php $*
	
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



main $@
