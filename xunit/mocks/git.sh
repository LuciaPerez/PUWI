tempFile=tempkiuhi34u45io.mock
okFile=tempOKOKOKOKOKOK.mock

function git {
	echo $FUNCNAME $@
	echo $FUNCNAME $@ >> $tempFile
	[ -f $okFile ]
}

function initGitMock {
	touch $tempFile
	touch $okFile
}

function setGitToFail {
	rm -f $okFile
}

function clearGitMock {
	rm $tempFile
	rm -f $okFile
}

function called {
	calls=`grep "^$1" $tempFile`
	[ "$calls" == "" ] && echo "no" || echo "yes"
}

function calledWith {
	calls=`grep "^$1 $2$" $tempFile`
	[ "$calls" == "" ] && echo "no" || echo "yes"
}
