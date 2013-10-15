#!/bin/bash


scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
projectDir=`pwd`

. $scriptDir/bash/functions.bash

x-www-browser http://localhost/PUWI/view/index.php?puwiDir=$scriptDir\&projectDir=$projectDir &

echo -e " PUWI running... \n"



