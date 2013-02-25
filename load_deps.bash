#!/bin/bash
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. $scriptDir/bash/messages.bash

dependencies=(
	phpunit 
	dbunit
	php-file-iterator
	php-text-template
	php-code-coverage
	php-token-stream
	php-timer
	phpunit-mock-objects
	phpunit-selenium
	phpunit-story
	php-invoker
)

function load_dep {
	text="Loading $1 ..."
	put " "
	put_red $1
	put " ...\n"
	[ -d vendor/$1 ] || git clone git://github.com/sebastianbergmann/$1.git vendor/$1 && end_with_ok || end_with_ko
}

[ -d vendor ] || mkdir vendor

#for dep in ${dependencies[@]}
#do
#	load_dep ${dep}
#done
if [ $# -gt 0 ];
	then echo -e "Loading dependencies..."
	for dep in $@
	do	
		load_dep ${dep}
	done
fi



