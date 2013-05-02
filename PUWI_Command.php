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
	include 'PUWI_LaunchBrowser.php';

	
    class PUWI_Command{

     	private $folder='';
     	private $pathProject='';
     	private $arrayFolders = array();
     	
	protected $arguments = array(
		'listGroups'              => FALSE,
		'loader'                  => NULL,
		'useDefaultConfiguration' => TRUE
	);

	/**
	* @var array
	*/
	protected $options = array();

	/**
	* @var array
	*/
	protected $longOptions = array(
		'colors' => NULL,
		'bootstrap=' => NULL,
		'configuration=' => NULL,
		'coverage-html=' => NULL,
		'coverage-clover=' => NULL,
		'coverage-php=' => NULL,
		'coverage-text==' => NULL,
		'debug' => NULL,
		'exclude-group=' => NULL,
		'filter=' => NULL,
		'testsuite=' => NULL,
		'group=' => NULL,
		'help' => NULL,
		'include-path=' => NULL,
		'list-groups' => NULL,
		'loader=' => NULL,
		'log-json=' => NULL,
		'log-junit=' => NULL,
		'log-tap=' => NULL,
		'process-isolation' => NULL,
		'repeat=' => NULL,
		'stderr' => NULL,
		'stop-on-error' => NULL,
		'stop-on-failure' => NULL,
		'stop-on-incomplete' => NULL,
		'stop-on-skipped' => NULL,
		'strict' => NULL,
		'tap' => NULL,
		'testdox' => NULL,
		'testdox-html=' => NULL,
		'testdox-text=' => NULL,
		'test-suffix=' => NULL,
		'no-configuration' => NULL,
		'no-globals-backup' => NULL,
		'printer=' => NULL,
		'static-backup' => NULL,
		'verbose' => NULL,
		'version' => NULL
	);

	/**
	* @var array	
	*/
	protected $missingExtensions = array();

	public static function main($exit = TRUE)
	{
		$puwi = new PUWI_Command;
		return $puwi->run($_SERVER['argv'],$new=TRUE, $exit);

	}
	
	protected function getFolderName($pathDir){
		return substr($pathDir,strlen($this->pathProject));
		
	}

	protected function getFolder($pathDir){
	   $regex="/^\./";
	   if (is_dir($pathDir)) { 

		$arrayFiles = array();
		if ($dir = opendir($pathDir)) { 
			while (($file = readdir($dir)) !== false) { 
				if (is_dir($pathDir . $file) && $file!="." && $file!=".." && !preg_match($regex,$file)){
					$this->folder= $pathDir . $file;
					$this->getFolder($this->folder . "/"); 
				} else{
					if($file!="." && $file!=".." && $file!=".." && !preg_match($regex,$file)){
						array_push($arrayFiles,$file);
						
					}
					if (count($arrayFiles) != 0){
						$folderName = $this->getFolderName($pathDir);
						$this->arrayFolders[$folderName]=$arrayFiles;				
					}
				}	

			}
			
			closedir($dir);	
		} 
	
		
	   } 

	}

	/**
	* @param array   $argv
	* @param boolean $exit
	*/
	public function run(array $argv, $new, $exit = TRUE)
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

		if ($this->arguments['listGroups']) {
		    PHPUnit_TextUI_TestRunner::printVersionString();

		    print "Available test group(s):\n";

		    $groups = $suite->getGroups();
		    sort($groups);

		    foreach ($groups as $group) {
			print " - $group\n";
		    }

		    if ($exit) {
			exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
		    } else {
			return PHPUnit_TextUI_TestRunner::SUCCESS_EXIT;
		    }
		}

		unset($this->arguments['test']);
		unset($this->arguments['testFile']);

		try {
			if (count($argv) >= 3){
				$result = $runner->doRunSingleTest($suite,$argv);
			}else{
		    	$result = $runner->doRun($suite, $this->arguments);
			}
		}

		catch (PHPUnit_Framework_Exception $e) {
		    print $e->getMessage() . "\n";
		}
		if (count($argv) < 3){
			$ret = PHPUnit_TextUI_TestRunner::FAILURE_EXIT;
	
			if (isset($result) && $result->wasSuccessful()) {
			    $ret = PHPUnit_TextUI_TestRunner::SUCCESS_EXIT;
			}
	
			else if (!isset($result) || $result->errorCount() > 0) {
			    $ret = PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT;
			}
		
		
			$this->pathProject = $argv[1];
			$this->getFolder($this->pathProject);
			//echo "\n----------FOLDERS-------------\n";
			//print_r($this->arrayFolders);
			
			
			//$new = (count($argv)==2)?TRUE:FALSE;
		
			$launch = new PUWI_LaunchBrowser();
			$rdo_launch = $launch->getResults($argv[1],$result,$this->arrayFolders,$new,$argv);
		}else{
			$rdo_launch=$result;
		}
		//TRAZA DEL ERROR!:
	        /*echo "TRAZA ERROR: ".$result->failureCount();
	        foreach ($result->failures() as $error) {
	        	echo "ERROR:";	        	
	        	echo $error->thrownException();
	        }*/

			
		if ($new == TRUE){
			if ($exit) {
			    exit($ret);
			} else {
			    return $ret;
			}
		}else{
			//echo a json de rdo_launch	
			return $rdo_launch;
		}
	
	}

    /**
     * Create a PHPUnit_TextUI_TestRunner, override in subclasses.
     *
     * @return PHPUnit_TextUI_PHPUnit_TextUI_TestRunner
     * @since  Method available since Release 3.6.0
     */
    protected function createRunner()
    {
        //return new PHPUnit_TextUI_TestRunner($this->arguments['loader']);
    	return new PUWI_Runner($this->arguments['loader']);
    }

    /**
     * Handles the command-line arguments.
     *
     * A child class of PHPUnit_TextUI_Command can hook into the argument
     * parsing by adding the switch(es) to the $longOptions array and point to a
     * callback method that handles the switch(es) in the child class like this
     *
     * <code>
     * <?php
     * class MyCommand extends PHPUnit_TextUI_Command
     * {
     *     public function __construct()
     *     {
     *         $this->longOptions['--my-switch'] = 'myHandler';
     *     }
     *
     *     // --my-switch foo -> myHandler('foo')
     *     protected function myHandler($value)
     *     {
     *     }
     * }
     * </code>
     *
     * @param array $argv
     */
    protected function handleArguments(array $argv)
    {
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

        foreach ($this->options[0] as $option) {
		
            switch ($option[0]) {

                case '--colors': {
                    $this->arguments['colors'] = TRUE;
                }
                break;

                case '--bootstrap': {
                    $this->arguments['bootstrap'] = $option[1];
                }
                break;

                case 'c':
                case '--configuration': {
                    $this->arguments['configuration'] = $option[1];
                }
                break;

                case '--coverage-clover':
                case '--coverage-html':
                case '--coverage-php':
                case '--coverage-text': {
                    if (!extension_loaded('tokenizer')) {
                        $this->showExtensionNotLoadedMessage(
                          'tokenizer', 'No code coverage will be generated.'
                        );

                        continue;
                    }

                    if (!extension_loaded('xdebug')) {
                        $this->showExtensionNotLoadedMessage(
                          'Xdebug', 'No code coverage will be generated.'
                        );

                        continue;
                    }

                    switch ($option[0]) {
                        case '--coverage-clover': {
                            $this->arguments['coverageClover'] = $option[1];
                        }
                        break;

                        case '--coverage-html': {
                            $this->arguments['reportDirectory'] = $option[1];
                        }
                        break;

                        case '--coverage-php': {
                            $this->arguments['coveragePHP'] = $option[1];
                        }
                        break;

                        case '--coverage-text': {
                            if ($option[1] === NULL) {
                                $option[1] = 'php://stdout';
                            }

                            $this->arguments['coverageText'] = $option[1];
                            $this->arguments['coverageTextShowUncoveredFiles'] = FALSE;
                            $this->arguments['coverageTextShowOnlySummary'] = FALSE;
                        }
                        break;
                    }
                }
                break;

                case 'd': {
                    $ini = explode('=', $option[1]);

                    if (isset($ini[0])) {
                        if (isset($ini[1])) {
                            ini_set($ini[0], $ini[1]);
                        } else {
                            ini_set($ini[0], TRUE);
                        }
                    }
                }
                break;

                case '--debug': {
                    $this->arguments['debug'] = TRUE;
                }
                break;

                case 'h':
                case '--help': {
                    $this->showHelp();
                    exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
                }
                break;

                case '--filter': {
                    $this->arguments['filter'] = $option[1];
                }
                break;

                case '--testsuite': {
                    $this->arguments['testsuite'] = $option[1];
                }
                break;

                case '--group': {
                    $this->arguments['groups'] = explode(',', $option[1]);
                }
                break;

                case '--exclude-group': {
                    $this->arguments['excludeGroups'] = explode(
                      ',', $option[1]
                    );
                }
                break;

                case '--test-suffix': {
                    $this->arguments['testSuffixes'] = explode(
                      ',', $option[1]
                    );
                }
                break;

                case '--include-path': {
                    $includePath = $option[1];
                }
                break;

                case '--list-groups': {
                    $this->arguments['listGroups'] = TRUE;
                }
                break;

                case '--printer': {
                    $this->arguments['printer'] = $option[1];
                }
                break;

                case '--loader': {
                    $this->arguments['loader'] = $option[1];
                }
                break;

                case '--log-json': {
                    $this->arguments['jsonLogfile'] = $option[1];
                }
                break;

                case '--log-junit': {
                    $this->arguments['junitLogfile'] = $option[1];
                }
                break;

                case '--log-tap': {
                    $this->arguments['tapLogfile'] = $option[1];
                }
                break;

                case '--process-isolation': {
                    $this->arguments['processIsolation'] = TRUE;
                }
                break;

                case '--repeat': {
                    $this->arguments['repeat'] = (int)$option[1];
                }
                break;

                case '--stderr': {
                    $this->arguments['stderr'] = TRUE;
                }
                break;

                case '--stop-on-error': {
                    $this->arguments['stopOnError'] = TRUE;
                }
                break;

                case '--stop-on-failure': {
                    $this->arguments['stopOnFailure'] = TRUE;
                }
                break;

                case '--stop-on-incomplete': {
                    $this->arguments['stopOnIncomplete'] = TRUE;
                }
                break;

                case '--stop-on-skipped': {
                    $this->arguments['stopOnSkipped'] = TRUE;
                }
                break;

                case '--tap': {
                    $this->arguments['printer'] = new PHPUnit_Util_Log_TAP;
                }
                break;

                case '--testdox': {
                    $this->arguments['printer'] = new PHPUnit_Util_TestDox_ResultPrinter_Text;
                }
                break;

                case '--testdox-html': {
                    $this->arguments['testdoxHTMLFile'] = $option[1];
                }
                break;

                case '--testdox-text': {
                    $this->arguments['testdoxTextFile'] = $option[1];
                }
                break;

                case '--no-configuration': {
                    $this->arguments['useDefaultConfiguration'] = FALSE;
                }
                break;

                case '--no-globals-backup': {
                    $this->arguments['backupGlobals'] = FALSE;
                }
                break;

                case '--static-backup': {
                    $this->arguments['backupStaticAttributes'] = TRUE;
                }
                break;

                case 'v':
                case '--verbose': {
                    $this->arguments['verbose'] = TRUE;
                }
                break;

                case '--version': {
                    PHPUnit_TextUI_TestRunner::printVersionString();
                    exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
                }
                break;

                case '--strict': {
                    $this->arguments['strict'] = TRUE;
                }
                break;

                default: {

                    $optionName = str_replace('--', '', $option[0]);

                    if (isset($this->longOptions[$optionName])) {
                        $handler = $this->longOptions[$optionName];
                    }

                    else if (isset($this->longOptions[$optionName . '='])) {
                        $handler = $this->longOptions[$optionName . '='];
                    }

                    if (isset($handler) && is_callable(array($this, $handler))) {
                        $this->$handler($option[1]);
                    }
                }
            }
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
            if (file_exists('phpunit.xml')) {
                $this->arguments['configuration'] = realpath('phpunit.xml');
            } else if (file_exists('phpunit.xml.dist')) {
                $this->arguments['configuration'] = realpath(
                  'phpunit.xml.dist'
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
                exit(PHPUnit_TextUI_PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
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

            $logging = $configuration->getLoggingConfiguration();

            if (isset($logging['coverage-html']) || isset($logging['coverage-clover']) || isset($logging['coverage-text']) ) {
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

    /**
     * Handles the loading of the PHPUnit_Runner_TestSuiteLoader implementation.
     *
     * @param  string  $loaderClass
     * @param  string  $loaderFile
     * @return PHPUnit_Runner_TestSuiteLoader
     */
    protected function handleLoader($loaderClass, $loaderFile = '')
    {
        if (!class_exists($loaderClass, FALSE)) {
            if ($loaderFile == '') {
                $loaderFile = PHPUnit_Util_Filesystem::classNameToFilename(
                  $loaderClass
                );
            }

            $loaderFile = stream_resolve_include_path($loaderFile);

            if ($loaderFile) {
                require $loaderFile;
            }
        }

        if (class_exists($loaderClass, FALSE)) {
            $class = new ReflectionClass($loaderClass);

            if ($class->implementsInterface('PHPUnit_Runner_TestSuiteLoader') &&
                $class->isInstantiable()) {
                $loader = $class->newInstance();
            }
        }

        if (!isset($loader)) {
            PHPUnit_TextUI_TestRunner::showError(
              sprintf(
                'Could not use "%s" as loader.',

                $loaderClass
              )
            );
        }

        return $loader;
    }

    /**
     * Handles the loading of the PHPUnit_Util_Printer implementation.
     *
     * @param  string $printerClass
     * @param  string $printerFile
     * @return PHPUnit_Util_Printer
     */
    protected function handlePrinter($printerClass, $printerFile = '')
    {
        if (!class_exists($printerClass, FALSE)) {
            if ($printerFile == '') {
                $printerFile = PHPUnit_Util_Filesystem::classNameToFilename(
                  $printerClass
                );
            }

            $printerFile = stream_resolve_include_path($printerFile);

            if ($printerFile) {
                require $printerFile;
            }
        }

        if (class_exists($printerClass, FALSE)) {
            $class = new ReflectionClass($printerClass);

            if ($class->implementsInterface('PHPUnit_Framework_TestListener') &&
                $class->isSubclassOf('PHPUnit_Util_Printer') &&
                $class->isInstantiable()) {
                if ($class->isSubclassOf('PHPUnit_TextUI_ResultPrinter')) {
                    return $printerClass;
                }

                $printer = $class->newInstance();
            }
        }

        if (!isset($printer)) {
            PHPUnit_TextUI_TestRunner::showError(
              sprintf(
                'Could not use "%s" as printer.',

                $printerClass
              )
            );
        }

        return $printer;
    }

    /**
     * Loads a bootstrap file.
     *
     * @param string $filename
     */
    protected function handleBootstrap($filename)
    {
        try {
            PHPUnit_Util_Fileloader::checkAndLoad($filename);
        }

        catch (PHPUnit_Framework_Exception $e) {
            PHPUnit_TextUI_TestRunner::showError($e->getMessage());
        }
    }

    /**
     * @param string  $message
     * @since Method available since Release 3.6.0
     */
    protected function showExtensionNotLoadedMessage($extension, $message = '')
    {
        if (isset($this->missingExtensions[$extension])) {
            return;
        }

        if (!empty($message)) {
            $message = ' ' . $message;
        }

        $this->showMessage(
          'The ' . $extension . ' extension is not loaded.' . $message . "\n",
          FALSE
        );

        $this->missingExtensions[$extension] = TRUE;
    }

    /**
     * Shows a message.
     *
     * @param string  $message
     * @param boolean $exit
     */
    protected function showMessage($message, $exit = TRUE)
    {
        PHPUnit_TextUI_TestRunner::printVersionString();
        print $message . "\n";

        if ($exit) {
            exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
        } else {
            print "\n";
        }
    }

    /**
     * Show the help message.
     */
    protected function showHelp()
    {
        PHPUnit_TextUI_TestRunner::printVersionString();

        print <<<EOT
Usage: phpunit [switches] UnitTest [UnitTest.php]
       phpunit [switches] <directory>

  --log-junit <file>        Log test execution in JUnit XML format to file.
  --log-tap <file>          Log test execution in TAP format to file.
  --log-json <file>         Log test execution in JSON format.

  --coverage-clover <file>  Generate code coverage report in Clover XML format.
  --coverage-html <dir>     Generate code coverage report in HTML format.
  --coverage-php <file>     Serialize PHP_CodeCoverage object to file.
  --coverage-text=<file>    Generate code coverage report in text format.
                            Default to writing to the standard output.

  --testdox-html <file>     Write agile documentation in HTML format to file.
  --testdox-text <file>     Write agile documentation in Text format to file.

  --filter <pattern>        Filter which tests to run.
  --testsuite <pattern>     Filter which testsuite to run.
  --group ...               Only runs tests from the specified group(s).
  --exclude-group ...       Exclude tests from the specified group(s).
  --list-groups             List available test groups.
  --test-suffix ...         Only search for test in files with specified
                            suffix(es). Default: Test.php,.phpt

  --loader <loader>         TestSuiteLoader implementation to use.
  --printer <printer>       TestSuiteListener implementation to use.
  --repeat <times>          Runs the test(s) repeatedly.

  --tap                     Report test execution progress in TAP format.
  --testdox                 Report test execution progress in TestDox format.

  --colors                  Use colors in output.
  --stderr                  Write to STDERR instead of STDOUT.
  --stop-on-error           Stop execution upon first error.
  --stop-on-failure         Stop execution upon first error or failure.
  --stop-on-skipped         Stop execution upon first skipped test.
  --stop-on-incomplete      Stop execution upon first incomplete test.
  --strict                  Run tests in strict mode.
  -v|--verbose              Output more verbose information.
  --debug                   Display debugging information during test execution.

  --process-isolation       Run each test in a separate PHP process.
  --no-globals-backup       Do not backup and restore \$GLOBALS for each test.
  --static-backup           Backup and restore static attributes for each test.

  --bootstrap <file>        A "bootstrap" PHP file that is run before the tests.
  -c|--configuration <file> Read configuration from XML file.
  --no-configuration        Ignore default configuration file (phpunit.xml).
  --include-path <path(s)>  Prepend PHP's include_path with given path(s).
  -d key[=value]            Sets a php.ini value.

  -h|--help                 Prints this usage information.
  --version                 Prints the version and exits.

EOT;
    }

    /**
     * Custom callback for test suite discovery.
     */
    protected function handleCustomTestSuite()
    {
    }
	}

	if (isset($_SERVER['argv'])){
		PUWI_Command::main();
	}

?>