<?php
	
	$matriz_ini = parse_ini_file("config.ini");
	define ('pathAutoload','pathAutoload');
	$pathAutoload = $matriz_ini[pathAutoload];

	define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main', 'pathAutoload');
	if (strpos('@php_bin@', '@php_bin') === 0) {
		require  $pathAutoload;
	} else {
		require '@php_dir@' . DIRECTORY_SEPARATOR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
	}
	
	include 'PUWI_Runner.php';
	include 'PUWI_GetResults.php';

	
    class PUWI_Command extends PHPUnit_TextUI_Command{

		/**
		* @param array   $argv
		* @param boolean $exit
		*/
		public function run(array $argv, $exit = TRUE)
		{
			$this->handleArguments($argv);
	
			$runner = $this->createRunner();
	
			if (is_object($this->arguments['test']) &&
			    $this->arguments['test'] instanceof PHPUnit_Framework_Test) {
			    $suite = $this->arguments['test'];
			} else {
			    $suite = $runner->getTest(
			      $this->arguments['test'],
			      $this->arguments['testFile'],
			      $this->arguments['testSuffixes']
			    );
			}

			unset($this->arguments['test']);
			unset($this->arguments['testFile']);
	
			try {
				if (count($argv) >= 3){
					$result = $runner->doRunSingleTest($suite,$argv,$this->arguments);
				}else{
			    	$result = $runner->doRun($suite, $argv,$this->arguments);
				}
			}
	
			catch (PHPUnit_Framework_Exception $e) {
			    print $e->getMessage() . "\n";
			}

			$results = new PUWI_GetResults();
			
			$arrayResults = $results->getResults($argv[1],$result,$argv,$suite);
			return $arrayResults;
	
		}
	
	    /**
	     * Create a PUWI_Runner.
	     *
	     * @return PUWI_Runner
	     */
	    protected function createRunner()
	    {
	    	return new PUWI_Runner($this->arguments['loader']);
	    }
	    
	    /**
	     * Handle arguments to configure phpunit execution
	     * 
	     * @param array   $argv
	     */
	    protected function handleArguments(array $argv)
	    {
	    	if (defined('__PHPUNIT_PHAR__')) {
	    		$this->longOptions['self-update'] = NULL;
	    	}
	    
	    	try {
	    		$this->options = PHPUnit_Util_Getopt::getopt(
	    				$argv,
	    				'd:c:hv',
	    				array_keys($this->longOptions)
	    		);
	    	}
	    
	    	catch (PHPUnit_Framework_Exception $e) {
	    		PHPUnit_TextUI_TestRunner::showError($e->getMessage());
	    	}
	    
	    	$this->handleCustomTestSuite();
	    
	    	if (!isset($this->arguments['test'])) {
	    
	    		if (isset($this->options[1][0])) {
	    			$this->arguments['test'] = $this->options[1][0];
	    		}
	    
	    		if (isset($this->options[1][1])) {
	    			$this->arguments['testFile'] = realpath($this->options[1][1]);
	    		} else {
	    			$this->arguments['testFile'] = '';
	    		}
	    
	    		if (isset($this->arguments['test']) &&
	    				is_file($this->arguments['test']) &&
	    				substr($this->arguments['test'], -5, 5) != '.phpt') {
	    			$this->arguments['testFile'] = realpath($this->arguments['test']);
	    			$this->arguments['test']     = substr($this->arguments['test'], 0, strrpos($this->arguments['test'], '.'));
	    		}
	    	}
	    
	    	if (!isset($this->arguments['testSuffixes'])) {
	    		$this->arguments['testSuffixes'] = array('Test.php', '.phpt');
	    	}
	    
	    	if (isset($includePath)) {
	    		ini_set(
	    		'include_path',
	    		$includePath . PATH_SEPARATOR . ini_get('include_path')
	    		);
	    	}
	    
	    	if (isset($this->arguments['bootstrap'])) {
	    		$this->handleBootstrap($this->arguments['bootstrap']);
	    	}
	    
	    	if (isset($this->arguments['printer']) &&
	    			is_string($this->arguments['printer'])) {
	    		$this->arguments['printer'] = $this->handlePrinter($this->arguments['printer']);
	    	}
	    
	    	if ($this->arguments['loader'] !== NULL) {
	    		$this->arguments['loader'] = $this->handleLoader($this->arguments['loader']);
	    	}

	    	if (isset($this->arguments['configuration']) &&
	    			is_dir($this->arguments['configuration'])) {
	    		$configurationFile = $this->arguments['configuration'] .
	    		'/phpunit.xml';
	    
	    		if (file_exists($configurationFile)) {
	    			$this->arguments['configuration'] = realpath(
	    					$configurationFile
	    			);
	    		}
	    
	    		else if (file_exists($configurationFile . '.dist')) {
	    			$this->arguments['configuration'] = realpath(
	    					$configurationFile . '.dist'
	    			);
	    			
	    		}

	    	}
	    	else if (!isset($this->arguments['configuration']) &&
	    			$this->arguments['useDefaultConfiguration']) {

	    		if (file_exists($argv[1].'phpunit.xml')) {
	    			$this->arguments['configuration'] = realpath($argv[1].'phpunit.xml');
	    		} else if (file_exists($argv[1].'phpunit.xml.dist')) {
	    			$this->arguments['configuration'] = realpath(
	    					$argv[1].'phpunit.xml.dist'
	    			);

	    		}
	    	}

	    	if (isset($this->arguments['configuration'])) {
	    		try {
	    			$configuration = PHPUnit_Util_Configuration::getInstance(
	    					$this->arguments['configuration']
	    			);
	    		}
	    
	    		catch (Exception $e) {
	    			print $e->getMessage() . "\n";
	    			exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
	    		}
	    
	    		$phpunit = $configuration->getPHPUnitConfiguration();
	    
	    		$configuration->handlePHPConfiguration();
	    
	    		if (!isset($this->arguments['bootstrap']) && isset($phpunit['bootstrap'])) {
	    			$this->handleBootstrap($phpunit['bootstrap']);
	    		}
	    
	    		if (isset($phpunit['printerClass'])) {
	    			if (isset($phpunit['printerFile'])) {
	    				$file = $phpunit['printerFile'];
	    			} else {
	    				$file = '';
	    			}
	    
	    			$this->arguments['printer'] = $this->handlePrinter(
	    					$phpunit['printerClass'], $file
	    			);
	    		}
	    
	    		if (isset($phpunit['testSuiteLoaderClass'])) {
	    			if (isset($phpunit['testSuiteLoaderFile'])) {
	    				$file = $phpunit['testSuiteLoaderFile'];
	    			} else {
	    				$file = '';
	    			}
	    
	    			$this->arguments['loader'] = $this->handleLoader(
	    					$phpunit['testSuiteLoaderClass'], $file
	    			);
	    		}
	    
	    		$browsers = $configuration->getSeleniumBrowserConfiguration();
	    
	    		if (!empty($browsers) &&
	    				class_exists('PHPUnit_Extensions_SeleniumTestCase')) {
	    			PHPUnit_Extensions_SeleniumTestCase::$browsers = $browsers;
	    		}
	    
	    		if (!isset($this->arguments['test'])) {
	    			$testSuite = $configuration->getTestSuiteConfiguration(isset($this->arguments['testsuite']) ? $this->arguments['testsuite'] : null);
	    
	    			if ($testSuite !== NULL) {
	    				$this->arguments['test'] = $testSuite;
	    			}
	    		}
	    	}
	    
	    	if (isset($this->arguments['test']) && is_string($this->arguments['test']) && substr($this->arguments['test'], -5, 5) == '.phpt') {
	    		$test = new PHPUnit_Extensions_PhptTestCase($this->arguments['test']);
	    
	    		$this->arguments['test'] = new PHPUnit_Framework_TestSuite;
	    		$this->arguments['test']->addTest($test);
	    	}
	    
	    	if (!isset($this->arguments['test']) ||
	    			(isset($this->arguments['testDatabaseLogRevision']) && !isset($this->arguments['testDatabaseDSN']))) {
	    		$this->showHelp();
	    		exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
	    	}
	    }
	     
	    
    }

?>
