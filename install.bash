#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. $scriptDir/bash/messages.bash
. $scriptDir/run.bash

echo -e "\nLoading configuration...\n"
cp $scriptDir/config.ini.inc $scriptDir/config.ini
end_with_ok 

function main {
	read_config
	if [ ${#vendor_deps[@]} -eq 0 ]; 
		then 
		echo "You don't have any available dependency to load in the default location. Review your config file ($scriptDir/config.ini)"
	else $scriptDir/load_deps.bash ${vendor_deps[*]} 
	fi
	
	createAlias
}

function read_config {
	regex="^.*\/vendor\/.*"
	
	while read line
		do 
		checkEnterSection && checkEnterDeps
		path=`echo $line | awk '{print $3}'`
		
		if  [ "$inDeps" == "yes" ] && [[ $path =~ $regex ]]; 
			then 
			searchDepsName $path
		fi
	done < $scriptDir/config.ini.inc

}

function searchDepsName {
	
	dep=`echo $1 | cut -d"/" -f3`
	index=`echo ${#vendor_deps[@]}`
	vendor_deps[$index]=$dep
}

function createAlias {	
	if [[ `grep "^alias puwi=" ~/.bashrc` = "" ]]
	then
		echo 'alias puwi='$scriptDir'/launch_browser.bash' >> ~/.bashrc && . ~/.bashrc 
	fi
}

main $@



