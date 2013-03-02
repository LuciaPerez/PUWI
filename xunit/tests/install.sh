#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

installScript=$scriptDir/../../install.bash
testeeScript=$scriptDir/temp/testee.bash

function setUp {
	mkdir $scriptDir/temp
	cp -Rf $scriptDir/../../bash $scriptDir/temp
	cp -Rf $scriptDir/../../run.bash $scriptDir/temp
	cp -Rf $scriptDir/../../config.ini $scriptDir/temp
	mockLoadDeps $scriptDir/temp
	head -n 1 $installScript > $testeeScript
	tail -n +2 $installScript >> $testeeScript
	chmod a+x $testeeScript
	fillConfigWith "[dependencies]\npathPhpunit = ./vendor/phpunit/\npathDbunit = ./vendor/dbunit/"
}

function mockLoadDeps {
	mockTempFile="temp23423u5g35y.temp"
	mockContent='tempFile='$mockTempFile';echo $@ > $tempFile'
	echo $mockContent > $1/load_deps.bash
	chmod a+x $1/load_deps.bash
}

function fillConfigWith {
	echo -e $1 > $scriptDir/temp/config.ini.inc
}

function tearDown {
	rm $testeeScript
	rm $mockTempFile
	rm -rf $scriptDir/temp
}

function testConfigIniIsCreated {
	output=$( $testeeScript 2> /dev/null )
	[ -f $scriptDir/temp/config.ini ] || fail "Expected config.ini to exist but it doesn't"
}

function testErrorIfNotAvailableDepsInVendor {
	fillConfigWith "[dependencies]\npathPhpunit = ./phpunit/"
	output=$( $testeeScript 2> /dev/null )
	assertMatches ".*You don't have any available dependency to load in the default location.*" "$output"
}

function testNoErrorIfAvailableDepsInVendor {
	output=$( $testeeScript 2> /dev/null )
	assertNotMatches ".*You don't have any available dependency to load in the default location.*" "$output"
}

function testLoadDepsIsCalledWithEachDep {
	output=$( $testeeScript 2> /dev/null )
	assertEquals "phpunit dbunit" "$( cat $mockTempFile )"
}

function testAddsAliasToBashrc {
	output=$( $testeeScript 2> /dev/null )
	assertFileContains ~/.bashrc "^alias puwi="
}
