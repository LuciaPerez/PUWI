tempFile=temp23i4uh234iu5.mock

function php {
	echo $FUNCNAME $@
	echo $FUNCNAME $@ > $tempFile
}

function initPhpMock {
	touch $tempFile
}

function clearPhpMock {
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
