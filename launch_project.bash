#!/bin/bash


scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
projectDir=`pwd`

$scriptDir/run.bash $projectDir/tests/ 



#x-www-browser "http://localhost/view/puwi.html" &
echo -e " PUWI running... \n"
