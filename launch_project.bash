#!/bin/bash


scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
projectDir=`pwd`

#$scriptDir/run.bash $projectDir/

x-www-browser http://localhost/PUWI/view/index.php &

echo -e " PUWI running... \n"



