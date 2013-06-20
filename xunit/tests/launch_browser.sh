#!/bin/bash

scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

launchBrowserScript=$scriptDir/../../launch_project.bash
. $scriptDir/../mocks/browser.sh

function setUp {
	initBrowserMock
	head -n 1 $launchBrowserScript > $launchBrowserScript.testee
	echo 'scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"' >> $launchBrowserScript.testee
	echo ". $scriptDir/../mocks/browser.sh" >> $launchBrowserScript.testee
	tail -n +2 $launchBrowserScript >> $launchBrowserScript.testee
	chmod a+x $launchBrowserScript.testee
	testeeScript=$launchBrowserScript.testee
}

function tearDown {
	rm $testeeScript
	clearBrowserMock
}

function testMessagePUWIrunning {
	output=$( $testeeScript )
	assertMatches "PUWI running\.\.\..*" "$output"
}

function testCallsBrowser {
	htmlFile=$( normalizePath "$scriptDir/../../view/index.html" )

	output=$( $testeeScript )
	assertEquals "yes" $( called "x-www-browser" )
}
