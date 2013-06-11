<?php

class PUWI_Runner extends PHPUnit_TextUI_TestRunner
{
    const SUCCESS_EXIT   = 0;
    const FAILURE_EXIT   = 1;
    const EXCEPTION_EXIT = 2;
	protected $passed = array();
	protected $failures = array();
	protected $errors = array();
	protected $skipped = array();
	protected $incomplete = array();
	protected $failedTests = array();
    /**
     * @var PHP_CodeCoverage_Filter
     */
    protected $codeCoverageFilter;

    /**
     * @var PHPUnit_Runner_TestSuiteLoader
     */
    protected $loader = NULL;


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

    public function doRunSingleTest($suite,$argv){
    	$results = new PUWI_GetResults();
    	$command = new PUWI_Command();
    	
    	//$folders = $command->getFolder($argv[1]);

    	$groups_info = $suite->getGroupDetails();
    	$total_groups = $results->getGroups($groups_info,$suite->getGroups());
    	
    	$arrayTests = $suite->tests();
    	$result = array();
    	$tests_passed = array();
    	foreach ($arrayTests as $test){
    		$singleTests=$test->tests();
    		foreach ($singleTests as $st){
    			switch ($argv[3]){
    				case "test":
    					if ($this->checkSingleTest($test->getName()."::".$st->getName(),$argv[2])){
    						$this->runSingleTest($st,$test->getName(),$argv);
    					}	
    				break;
    				case "file":
    					if ($test->getName() == $argv[2]){
    						$this->runSingleTest($st,$argv[2],$argv);
    					}
    				break;
    				
    				case 'group':	
    					if(in_array($argv[2],array_keys($total_groups))){
		    				foreach($total_groups[$argv[2]] as $single_test){
		    					$className = strstr($single_test, ':', true);
		    					if ($this->checkSingleTest($test->getName()."::".$st->getName(), $single_test)){	
		    						$this->runSingleTest($st,$className,$argv);
		    					}
		    				}
    					}
    				break;
    			}
    		}
    		
    	}
    	$result['passed'] = $this->passed;
    	$result['failures'] = $this->failures;
    	$result['errors'] = $this->errors;
    	$result['incomplete'] = $this->incomplete;
    	$result['skipped'] = $this->skipped;
    	$result['failedTests'] = $this->failedTests;
    	
    	$result['groups'] = $total_groups;
    	$result['folders'] = $results->getArrayFolders($argv[1]);
    	return $result;
    }
    
    protected function checkSingleTest($test, $required_test){
    	$res = ($test == $required_test) ? true : false;
    	return $res;
    }
    
    protected function runSingleTest($single_test,$class_name,$argv){
    	$results = new PUWI_GetResults();
    	$resultRun = $single_test->run();
    	
    	if (count($resultRun->passed())!=0){ 
    		array_push($this->passed,$class_name."::".$single_test->getName());
    	}else{
    		if (count($resultRun->notImplemented())!=0){
    			array_push($this->incomplete,$class_name."::".$single_test->getName());
    		}else{
    			if (count($resultRun->skipped())!=0){
    				array_push($this->skipped,$class_name."::".$single_test->getName());
    			}else{
    				if (count($resultRun->failures())!=0){
    					array_push($this->failures,$class_name."::".$single_test->getName());
    					$info = $results->getFails($resultRun->failures(),true);
    					array_push($this->failedTests,$info); 
    				}else{
    					array_push($this->errors,$class_name."::".$single_test->getName());
    				}
    			}
    		}

    	}

    }
    /**
     * @param  PHPUnit_Framework_Test $suite
     * @param  array                  $arguments
     * @return PHPUnit_Framework_TestResult
     */
    public function doRun(PHPUnit_Framework_Test $suite, array $arguments = array())
    {
        $this->handleConfiguration($arguments);

        $this->processSuiteFilters($suite, $arguments);

        if (isset($arguments['bootstrap'])) {
            $GLOBALS['__PHPUNIT_BOOTSTRAP'] = $arguments['bootstrap'];
        }

        if ($arguments['backupGlobals'] === FALSE) {
            $suite->setBackupGlobals(FALSE);
        }

        if ($arguments['backupStaticAttributes'] === TRUE) {
            $suite->setBackupStaticAttributes(TRUE);
        }

        if (is_integer($arguments['repeat'])) {
            $test = new PHPUnit_Extensions_RepeatedTest(
              $suite,
              $arguments['repeat'],
              $arguments['processIsolation']
            );

            $suite = new PHPUnit_Framework_TestSuite();
            $suite->addTest($test);
        }

        $result = $this->createTestResult();

        if (!$arguments['convertErrorsToExceptions']) {
            $result->convertErrorsToExceptions(FALSE);
        }

        if (!$arguments['convertNoticesToExceptions']) {
            PHPUnit_Framework_Error_Notice::$enabled = FALSE;
        }

        if (!$arguments['convertWarningsToExceptions']) {
            PHPUnit_Framework_Error_Warning::$enabled = FALSE;
        }

        if ($arguments['stopOnError']) {
            $result->stopOnError(TRUE);
        }

        if ($arguments['stopOnFailure']) {
            $result->stopOnFailure(TRUE);
        }

        if ($arguments['stopOnIncomplete']) {
            $result->stopOnIncomplete(TRUE);
        }

        if ($arguments['stopOnSkipped']) {
            $result->stopOnSkipped(TRUE);
        }


        foreach ($arguments['listeners'] as $listener) {
            $result->addListener($listener);
        }


        if (isset($arguments['testdoxHTMLFile'])) {
            $result->addListener(
              new PHPUnit_Util_TestDox_ResultPrinter_HTML(
                $arguments['testdoxHTMLFile']
              )
            );
        }

        if (isset($arguments['testdoxTextFile'])) {
            $result->addListener(
              new PHPUnit_Util_TestDox_ResultPrinter_Text(
                $arguments['testdoxTextFile']
              )
            );
        }

        $codeCoverageReports = 0;

        if (extension_loaded('xdebug')) {
            if (isset($arguments['coverageClover'])) {
                $codeCoverageReports++;
            }

            if (isset($arguments['reportDirectory'])) {
                $codeCoverageReports++;
            }

            if (isset($arguments['coveragePHP'])) {
                $codeCoverageReports++;
            }

            if (isset($arguments['coverageText'])) {
                $codeCoverageReports++;
            }
        }

        if ($codeCoverageReports > 0) {
            $codeCoverage = new PHP_CodeCoverage(
              NULL, $this->codeCoverageFilter
            );

            $codeCoverage->setAddUncoveredFilesFromWhitelist(
              $arguments['addUncoveredFilesFromWhitelist']
            );

            $codeCoverage->setCheckForUnintentionallyCoveredCode(
              $arguments['strict']
            );

            $codeCoverage->setProcessUncoveredFilesFromWhitelist(
              $arguments['processUncoveredFilesFromWhitelist']
            );

            if (isset($arguments['forceCoversAnnotation'])) {
                $codeCoverage->setForceCoversAnnotation(
                  $arguments['forceCoversAnnotation']
                );
            }

            if (isset($arguments['mapTestClassNameToCoveredClassName'])) {
                $codeCoverage->setMapTestClassNameToCoveredClassName(
                  $arguments['mapTestClassNameToCoveredClassName']
                );
            }

            $result->setCodeCoverage($codeCoverage);
        }

        if ($codeCoverageReports > 1) {
            if (isset($arguments['cacheTokens'])) {
                $codeCoverage->setCacheTokens($arguments['cacheTokens']);
            }
        }

        if (isset($arguments['jsonLogfile'])) {
            $result->addListener(
              new PHPUnit_Util_Log_JSON($arguments['jsonLogfile'])
            );
        }

        if (isset($arguments['tapLogfile'])) {
            $result->addListener(
              new PHPUnit_Util_Log_TAP($arguments['tapLogfile'])
            );
        }

        if (isset($arguments['junitLogfile'])) {
            $result->addListener(
              new PHPUnit_Util_Log_JUnit(
                $arguments['junitLogfile'], $arguments['logIncompleteSkipped']
              )
            );
        }

        if ($arguments['strict']) {
            $result->strictMode(TRUE);

            $result->setTimeoutForSmallTests(
              $arguments['timeoutForSmallTests']
            );

            $result->setTimeoutForMediumTests(
              $arguments['timeoutForMediumTests']
            );

            $result->setTimeoutForLargeTests(
              $arguments['timeoutForLargeTests']
            );
        }

        if ($suite instanceof PHPUnit_Framework_TestSuite) {
            $suite->setRunTestInSeparateProcess($arguments['processIsolation']);
        }
        
        $suite->run($result);

        unset($suite);
        $result->flushListeners();

        return $result;
    }

}
