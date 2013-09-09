<?php

class PUWI_Runner extends PHPUnit_TextUI_TestRunner
{

    public function doRunSingleTest(PHPUnit_Framework_Test $suite,array $argv,$arguments){
    	
    	$mySuite = new PHPUnit_Framework_TestSuite;
    	
    	$test_suite = $suite->tests();
    	$result = array();

    	foreach ($test_suite as $test_case){ 
    		$singleTests=$test_case->tests();
    		
    		foreach ($singleTests as $st){
    			$c = get_class($st);
    			$checkingName = ($c == "PHPUnit_Framework_TestSuite_DataProvider") ? $st->getName() : $test_case->getName()."::".$st->getName();
    			switch ($argv[3]){
    				case "test":
    					if ($this->checkSingleTest($checkingName,$argv[2])){
    						$mySuite->addTest($st);
    					}	
    				break;
    				case "file":
    					if ($test_case->getName() == $argv[2]){
    						$mySuite->addTest($st);
    					}
    				break;
    				
    				case 'group':	
    					$results = new PUWI_GetResults();
    					$groups_info = $suite->getGroupDetails();
    					$total_groups = $results->getGroups($groups_info,$suite->getGroups());
    					
    					if(in_array($argv[2],array_keys($total_groups))){
		    				foreach($total_groups[$argv[2]] as $single_test){			
		    					if ($this->checkSingleTest($checkingName, $single_test)){
		    						$mySuite->addTest($st);
		    					}
		    				}
    					}
    				break;
    			}
    		}
    		
    	}
    	$result = $this->doRun($mySuite, $argv,$arguments);

    	return $result;
    }
    
    private function checkSingleTest($test, $required_test){
    	$res = ($test == $required_test) ? true : false;
    	return $res;
    }
    
    
    private function processSuiteFilters(PHPUnit_Framework_TestSuite $suite, array $arguments) {

    	if (!$arguments['filter'] &&
    			empty($arguments['groups']) &&
    			empty($arguments['excludeGroups'])) {
    		return;
    	}
    
    	$filterFactory = new PHPUnit_Runner_Filter_Factory();
    
    	if(!empty($arguments['excludeGroups'])) {
    		$filterFactory->addFilter(
    				new ReflectionClass('PHPUnit_Runner_Filter_Group_Exclude'),
    				$arguments['excludeGroups']
    		);
    	}
    
    	if(!empty($arguments['groups'])) {
    		$filterFactory->addFilter(
    				new ReflectionClass('PHPUnit_Runner_Filter_Group_Include'),
    				$arguments['groups']
    		);
    	}
    
    	if($arguments['filter']) {
    		$filterFactory->addFilter(
    				new ReflectionClass('PHPUnit_Runner_Filter_Test'),
    				$arguments['filter']
    		);
    	}
    	$suite->injectFilter($filterFactory);
    }
    /**
     * @param  PHPUnit_Framework_Test $suite
     * @param  array   			$arguments
     * @return PHPUnit_Framework_TestResult
     */
    public function doRun(PHPUnit_Framework_Test $suite, array $arguments = array(),$full_arguments)
    {

    	parent::handleConfiguration($full_arguments);
	
    	$this->processSuiteFilters($suite, $full_arguments);
    	
    	if (isset($full_arguments['bootstrap'])) {
    		$GLOBALS['__PHPUNIT_BOOTSTRAP'] = $full_arguments['bootstrap'];
    	}
    	
    	if ($full_arguments['backupGlobals'] === FALSE) {
    		$suite->setBackupGlobals(FALSE);
    	}
    	
    	if ($full_arguments['backupStaticAttributes'] === TRUE) {
    		$suite->setBackupStaticAttributes(TRUE);
    	}
    	
    	if (is_integer($full_arguments['repeat'])) {
    		$test = new PHPUnit_Extensions_RepeatedTest(
    				$suite,
    				$arguments['repeat'],
    				$arguments['processIsolation']
    		);
    	
    		$suite = new PHPUnit_Framework_TestSuite();
    		$suite->addTest($test);
    	}
    	
    	$result = parent::createTestResult();
    	
    	if (!$full_arguments['convertErrorsToExceptions']) {
    		$result->convertErrorsToExceptions(FALSE);
    	}
    	
    	if (!$full_arguments['convertNoticesToExceptions']) {
    		PHPUnit_Framework_Error_Notice::$enabled = FALSE;
    	}
    	
    	if (!$full_arguments['convertWarningsToExceptions']) {
    		PHPUnit_Framework_Error_Warning::$enabled = FALSE;
    	}
    	
    	if ($full_arguments['stopOnError']) {
    		$result->stopOnError(TRUE);
    	}
    	
    	if ($full_arguments['stopOnFailure']) {
    		$result->stopOnFailure(TRUE);
    	}
    	
    	if ($full_arguments['stopOnIncomplete']) {
    		$result->stopOnIncomplete(TRUE);
    	}
    	
    	if ($full_arguments['stopOnSkipped']) {
    		$result->stopOnSkipped(TRUE);
    	}
    	
    	foreach ($full_arguments['listeners'] as $listener) {
    		$result->addListener($listener);
    	}
    	
    	if (isset($full_arguments['testdoxHTMLFile'])) {
    		$result->addListener(
    				new PHPUnit_Util_TestDox_ResultPrinter_HTML(
    						$full_arguments['testdoxHTMLFile']
    				)
    		);
    	}
    	
    	if (isset($full_arguments['testdoxTextFile'])) {
    		$result->addListener(
    				new PHPUnit_Util_TestDox_ResultPrinter_Text(
    						$full_arguments['testdoxTextFile']
    				)
    		);
    	}
    	
    	$codeCoverageReports = 0;
    	
    	if (extension_loaded('tokenizer') && extension_loaded('xdebug')) {
    		if (isset($full_arguments['coverageClover'])) {
    			$codeCoverageReports++;
    		}
    	
    		if (isset($full_arguments['coverageCrap4J'])) {
    			$codeCoverageReports++;
    		}
    	
    		if (isset($full_arguments['coverageHtml'])) {
    			$codeCoverageReports++;
    		}
    	
    		if (isset($full_arguments['coveragePHP'])) {
    			$codeCoverageReports++;
    		}
    	
    		if (isset($full_arguments['coverageText'])) {
    			$codeCoverageReports++;
    		}
    	
    		if (isset($full_arguments['coverageXml'])) {
    			$codeCoverageReports++;
    		}
    	} else {
    		if (!extension_loaded('tokenizer')) {
    			$this->showExtensionNotLoadedMessage(
    					'tokenizer', 'No code coverage will be generated.'
    			);
    		}
    	
    		else if (!extension_loaded('Xdebug')) {
    			$this->showExtensionNotLoadedMessage(
    					'Xdebug', 'No code coverage will be generated.'
    			);
    		}
    	}
    	
    	if ($codeCoverageReports > 0) {
    		$codeCoverage = new PHP_CodeCoverage(
    				NULL, $this->codeCoverageFilter
    		);
    	
    		$codeCoverage->setAddUncoveredFilesFromWhitelist(
    				$full_arguments['addUncoveredFilesFromWhitelist']
    		);
    	
    		$codeCoverage->setCheckForUnintentionallyCoveredCode(
    				$full_arguments['strict']
    		);
    	
    		$codeCoverage->setProcessUncoveredFilesFromWhitelist(
    				$full_arguments['processUncoveredFilesFromWhitelist']
    		);
    	
    		if (isset($full_arguments['forceCoversAnnotation'])) {
    			$codeCoverage->setForceCoversAnnotation(
    					$full_arguments['forceCoversAnnotation']
    			);
    		}
    	
    		if (isset($full_arguments['mapTestClassNameToCoveredClassName'])) {
    			$codeCoverage->setMapTestClassNameToCoveredClassName(
    					$full_arguments['mapTestClassNameToCoveredClassName']
    			);
    		}
    	
    		$result->setCodeCoverage($codeCoverage);
    	}
    	
    	if ($codeCoverageReports > 1) {
    		if (isset($full_arguments['cacheTokens'])) {
    			$codeCoverage->setCacheTokens($full_arguments['cacheTokens']);
    		}
    	}
    	
    	if (isset($full_arguments['jsonLogfile'])) {
    		$result->addListener(
    				new PHPUnit_Util_Log_JSON($full_arguments['jsonLogfile'])
    		);
    	}
    	
    	if (isset($full_arguments['tapLogfile'])) {
    		$result->addListener(
    				new PHPUnit_Util_Log_TAP($full_arguments['tapLogfile'])
    		);
    	}
    	
    	if (isset($full_arguments['junitLogfile'])) {
    		$result->addListener(
    				new PHPUnit_Util_Log_JUnit(
    						$full_arguments['junitLogfile'], $full_arguments['logIncompleteSkipped']
    				)
    		);
    	}
    	
    	if ($full_arguments['strict']) {
    		$result->strictMode(TRUE);
    	
    		$result->setTimeoutForSmallTests(
    				$full_arguments['timeoutForSmallTests']
    		);
    	
    		$result->setTimeoutForMediumTests(
    				$full_arguments['timeoutForMediumTests']
    		);
    	
    		$result->setTimeoutForLargeTests(
    				$full_arguments['timeoutForLargeTests']
    		);
    	}
    	
    	if ($suite instanceof PHPUnit_Framework_TestSuite) {
    		$suite->setRunTestInSeparateProcess($full_arguments['processIsolation']);
    	}
    	
    	$suite->run($result);
    	
    	unset($suite);
    	$result->flushListeners();
    	
    	if (isset($codeCoverage)) {
    		if (isset($full_arguments['coverageClover'])) {
    			$writer = new PHP_CodeCoverage_Report_Clover;
    			$writer->process($codeCoverage, $full_arguments['coverageClover']);

    			unset($writer);
    		}
    	
    		if (isset($full_arguments['coverageCrap4J'])) {
    			$writer = new PHP_CodeCoverage_Report_Crap4j;
    			$writer->process($codeCoverage, $full_arguments['coverageCrap4J']);
    	
    			unset($writer);
    		}
    	
    		if (isset($full_arguments['coverageHtml'])) {
    			$writer = new PHP_CodeCoverage_Report_HTML(
    					$full_arguments['reportCharset'],
    					$full_arguments['reportHighlight'],
    					$full_arguments['reportLowUpperBound'],
    					$full_arguments['reportHighLowerBound'],
    					sprintf(
    							' and <a href="http://phpunit.de/">PHPUnit %s</a>',
    							PHPUnit_Runner_Version::id()
    					)
    			);
    			$writer->process($codeCoverage, $full_arguments['coverageHtml']);
    	
    			unset($writer);
    		}
    	
    		if (isset($full_arguments['coveragePHP'])) {
    			$writer = new PHP_CodeCoverage_Report_PHP;
    			$writer->process($codeCoverage, $full_arguments['coveragePHP']);

    			unset($writer);
    		}
    	
    	
    		if (isset($full_arguments['coverageXml'])) {
    			$writer = new PHP_CodeCoverage_Report_XML;
    			$writer->process($codeCoverage, $full_arguments['coverageXml']);
    	
    			unset($writer);
    		}
    	}
    	
    	return $result;
    }

}
