#!/bin/bash
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. $scriptDir/bash/messages.bash

echo -e "Loading dependencies..."

curl -O http://getcomposer.org/composer.phar
php composer.phar install --dev
