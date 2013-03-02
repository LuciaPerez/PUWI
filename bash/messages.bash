#!/bin/bash

columns=$(tput cols)

function put {
	echo -ne "$1"
}

function put_red {
	echo -ne "\E[31m"
	echo -ne "$1"
	tput sgr0
}

function end_with_ok {
	echo -ne "\e[s\e[$((columns-5))C[\E[32mok"
	tput sgr0
	echo -ne "]\e[u"
	echo 
}

function end_with_ko {
	echo -ne "\e[s\e[$((columns-5))C[\E[31mko"
	tput sgr0
	echo -ne "]\e[u"
	echo 
}

