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
			
			
				$results = new PUWI_GetResults();
	
				$arrayResults = $results->getResults($argv[1],$result,$argv);
			}else{
				$arrayResults=$result;
			}
	
			return $arrayResults;
	
		}
	
	    /**
	     * Create a PHPUnit_TextUI_TestRunner, override in subclasses.
	     *
	     * @return PHPUnit_TextUI_PHPUnit_TextUI_TestRunner
	     * @since  Method available since Release 3.6.0
	     */
	    protected function createRunner()
	    {
	    	return new PUWI_Runner($this->arguments['loader']);
	    }
    }

?>
