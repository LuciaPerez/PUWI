#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. $scriptDir/bash/messages.bash

echo -e "\nLoading configuration...\n"
end_with_ok 
cp $scriptDir/config.ini.inc $scriptDir/config.ini


function read_config {
	regex="^.*\/vendor\/.*"
	
	while read line
		do 
		path=`echo $line | awk '{print $3}'`
		if [[ $path =~ $regex ]]; 
			then 
			index=`echo ${#vendor_deps[@]}`
			vendor_deps[$index]=`echo $line | awk '{print $1}'`
		fi
	done < $scriptDir/config.ini.inc

}


echo -e "Do you want to load all PHPUnit dependencies in the default location (vendor/)? (Y/N)"
read ans

if [ $ans = 'Y' ] || [ $ans = 'y' ] || [ $ans = 'yes' ] || [ $ans = 'YES' ];  
	then read_config
	if [ ${#vendor_deps[@]} -eq 0 ]; 
	then echo "You don't have any available dependency to load in the default location. Review your config file ($scriptDir/config.ini)"
	fi

else vi $scriptDir/config.ini
     read_config
fi
$scriptDir/load_deps.bash ${vendor_deps[*]} 
echo -e "\nPUWI successfully installed!"






