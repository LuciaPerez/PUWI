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

echo -e "Loading dependencies..."

curl -sS https://getcomposer.org/installer | php
php composer.phar install



