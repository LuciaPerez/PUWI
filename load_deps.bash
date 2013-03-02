#!/bin/bash
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. $scriptDir/bash/messages.bash

function load_dep {
	text="Loading $1 ..."
	put " "
	put_red $1
	put " ...\n"
	[ -d $scriptDir/vendor/$1 ] || git clone git://github.com/sebastianbergmann/$1.git $scriptDir/vendor/$1 && end_with_ok || end_with_ko
}

[ -d $scriptDir/vendor ] || mkdir $scriptDir/vendor

if [ $# -gt 0 ];
	then echo -e "Loading dependencies..."
	for dep in $@
	do	
		load_dep ${dep}
	done
fi



