#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

loadDepsScript=$scriptDir/../../load_deps.bash
testeeScript=$scriptDir/temp/testee.bash
. $scriptDir/../mocks/git.sh

function setUp {
	initGitMock
	mkdir $scriptDir/temp
	cp -Rf $scriptDir/../../bash $scriptDir/temp
	head -n 1 $loadDepsScript > $testeeScript
	echo 'scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"' >> $testeeScript
	echo ". $scriptDir/../mocks/git.sh" >> $testeeScript
	tail -n +2 $loadDepsScript >> $testeeScript
	chmod a+x $testeeScript
}

function tearDown {
	rm $testeeScript
	rm -rf $scriptDir/temp
	clearGitMock
}

function testDoesntCallGitCloneWhenNoParameters {
	output=$( $testeeScript )
	assertEquals "no" $( called "git" )
}

function testCallsGitCloneWithParameter {
	aParameter="aSampleParameter"
	output=$( $testeeScript $aParameter )
	assertEquals "yes" $( called "git" )
	assertEquals "yes" $( calledWith "git" "clone git://github\.com/sebastianbergmann/.*$aParameter\.git .*vendor/.*" )
}

function testCallsGitCloneWithEachParameter {
	aParameter="aSampleParameter"
	anotherParameter="anotherSampleParameter"
	output=$( $testeeScript $aParameter $anotherParameter )
	assertEquals "yes" $( called "git" )
	assertEquals "yes" $( calledWith "git" "clone git://github\.com/sebastianbergmann/.*$aParameter\.git .*vendor/.*" )
	assertEquals "yes" $( calledWith "git" "clone git://github\.com/sebastianbergmann/.*$anotherParameter\.git .*vendor/.*" )
}

function testIfVendorDoesntExistItIsCreated {
	rm -rf $scriptDir/temp/vendor
	output=$( $testeeScript )
	[ -d $scriptDir/temp/vendor ] || fail "Expected $scriptDir/temp/vendor to have been created"
}

function testIfCloneOkPromptOK {
	aParameter="aSampleParameter"
	output=$( $testeeScript $aParameter )
	assertMatches ".*ok.*" "$output"
}

function testIfCloneFailsPromptKO {
	setGitToFail
	aParameter="aSampleParameter"
	output=$( $testeeScript $aParameter )
	assertMatches ".*ko.*" "$output"
}
