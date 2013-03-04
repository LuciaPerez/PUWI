#!/bin/bash


scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
projectDir=`pwd`

$scriptDir/run.bash $projectDir/tests/ 

echo -e " PUWI running... \n"
