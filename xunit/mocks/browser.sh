tempFile=tempj2h4f2hj3g4f.mock

function x-www-browser {
	echo $FUNCNAME $@ > $tempFile
}

function initBrowserMock {
	touch $tempFile
}

function clearBrowserMock {
	rm $tempFile
}

function called {
	calls=`grep "^$1" $tempFile`
	[ "$calls" == "" ] && echo "no" || echo "yes"
}

function calledWith {
	calls=`grep "^$1 $2$" $tempFile`
	[ "$calls" == "" ] && echo "no" || echo "yes"
}
