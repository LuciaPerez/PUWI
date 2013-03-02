#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

loadDepsScript=$scriptDir/../../../load_deps.bash
testeeScript=$scriptDir/temp/testee.bash

function setUp {
	mkdir $scriptDir/temp
	cp -Rf $scriptDir/../../../bash $scriptDir/temp
	head -n 1 $loadDepsScript > $testeeScript
	tail -n +2 $loadDepsScript >> $testeeScript
	chmod a+x $testeeScript
}

function tearDown {
	rm $testeeScript
	rm -rf $scriptDir/temp
}

# @dataProvider valid_repos
function testClonesGivenParameter {
	aParameter="$1"
	output=$( $testeeScript $aParameter 2> /dev/null )
	[ -d $scriptDir/temp/vendor/$aParameter ] || fail "Expected $aParameter to have been cloned"
}

function valid_repos {
	data "phpunit"
	data "dbunit"
}

# @dataProvider invalid_repos
function testCloneFailsWhenWrongRepo {
	aParameter="$1"
	output=$( $testeeScript $aParameter 2> /dev/null )
	[ -d $scriptDir/temp/vendor/$aParameter ] && fail "Expected $aParameter to NOT have been cloned"
}

function invalid_repos {
	data "invalidOne"
	data "anotherInvalid"
}
