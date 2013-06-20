#!/bin/bash
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. $scriptDir/bash/messages.bash


[ -d $scriptDir/vendor ] || mkdir $scriptDir/vendor

echo -e "Loading dependencies..."

cd $scriptDir/vendor/
git clone git://github.com/sebastianbergmann/phpunit.git

cd phpunit
curl -O http://getcomposer.org/composer.phar
php composer.phar install --dev
