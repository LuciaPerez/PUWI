#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

runScript=$scriptDir/../../run.bash
testeeScript=$scriptDir/temp/testee.bash
ourEmulatedDep="pathPhpunit"
ourEmulatedDepPath="./ourPathToPhpunit/"
. $scriptDir/../mocks/php.sh

function setUp {
	initPhpMock
	mkdir $scriptDir/temp
	echo -e "[dependencies]\n$ourEmulatedDep = $ourEmulatedDepPath" > $scriptDir/temp/config.ini
	head -n 1 $runScript > $testeeScript
	echo 'scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"' >> $testeeScript
	echo ". $scriptDir/../mocks/php.sh" >> $testeeScript
	tail -n +2 $runScript >> $testeeScript
	chmod a+x $testeeScript
}

function tearDown {
	rm $testeeScript
	rm -rf $scriptDir/temp
	clearPhpMock
}

function testRunCallsPhpWithConfigPaths {
	output=$( $testeeScript )
	assertEquals "yes" $( called "php" )
	assertEquals "yes" $( calledWith "php" "-d include_path=.:$ourEmulatedDepPath:\([^:]*:\)*[^:]* /.*/PUWI_Runner.php" )
}

function testRunPipesParametersIntoPhp {
	aParameter="aFile"
	output=$( $testeeScript $aParameter )
	assertEquals "yes" $( called "php" )
	assertEquals "yes" $( calledWith "php" "-d include_path=.:$ourEmulatedDepPath:\([^:]*:\)*[^:]* /.*/PUWI_Runner.php $aParameter" )
}

function testRunIncludesPhpDir {
	phpDir=`pear config-show 2> /dev/null | grep php_dir | awk '{print $NF}'`
	output=$( $testeeScript )
	assertEquals "yes" $( called "php" )
	assertEquals "yes" $( calledWith "php" "-d include_path=.*$phpDir.*" )
}
