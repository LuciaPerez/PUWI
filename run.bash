#!/bin/bash
#php -d include_path='.:./vendor/phpunit/:./vendor/dbunit/:./vendor/php-code-coverage/:./vendor/php-file-iterator/:./vendor/php-invoker/:./vendor/php-text-template/:./vendor/php-timer:./vendor/php-token-stream:./vendor/phpunit-mock-objects/:./vendor/phpunit-selenium/:./vendor/phpunit-story/:./vendor/version:/usr/share/php' ./vendor/phpunit/phpunit.php $*


#Llamada a phpunit.php
#php -d include_path='.:/opt/phpunit/phpunit/:/opt/phpunit/dbunit/:/opt/phpunit/php-code-coverage/:/opt/phpunit/php-file-iterator/:/opt/phpunit/php-invoker/:/opt/phpunit/php-text-template/:/opt/phpunit/php-timer:/opt/phpunit/php-token-stream:/opt/phpunit/phpunit-mock-objects/:/opt/phpunit/phpunit-selenium/:/opt/phpunit/phpunit-story/:/opt/phpunit/version:/usr/share/php' /opt/phpunit/phpunit/phpunit.php $*


#PUWI_Runner.php
scriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

while read line
do 
	key=`echo $line | awk '{print $1}'`
	case $key in
		pathPhpunit) pathPhpunit=`echo $line | awk '{print $3}'`;;
		pathDbunit) pathDbunit=`echo $line | awk '{print $3}'`;;
		pathCodeCoverage) pathCodeCoverage=`echo $line | awk '{print $3}'`;;
		pathFileIterator) pathFileIterator=`echo $line | awk '{print $3}'`;;
		pathInvoker) pathInvoker=`echo $line | awk '{print $3}'`;;
		pathTextTemplate) pathTextTemplate=`echo $line | awk '{print $3}'`;;
		pathTimer) pathTimer=`echo $line | awk '{print $3}'`;;
		pathTokenStream) pathTokenStream=`echo $line | awk '{print $3}'`;;
		pathMockObjects) pathMockObjects=`echo $line | awk '{print $3}'`;;
		pathSelenium) pathSelenium=`echo $line | awk '{print $3}'`;;
		pathStory) pathStory=`echo $line | awk '{print $3}'`;;
		pathVersion) pathVersion=`echo $line | awk '{print $3}'`;;
		pathPear) pathPear=`echo $line | awk '{print $3}'`;;
	esac

done < $scriptDir/config.ini

php -d include_path='.:'$pathPhpunit':'$pathDbunit':'$pathCodeCoverage':'$pathFileIterator':'$pathInvoker':'$pathTextTemplate':'$pathTimer':'$pathTokenStream':'$pathMockObjects':'$pathSelenium':'$pathStory':'$pathVersion':'$pathPear'' $scriptDir/PUWI_Runner.php $*







