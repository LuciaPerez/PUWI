<?php
	
	$matriz_ini = parse_ini_file("config.ini");
	define ('pathAutoload','pathAutoload');
	$pathAutoload = $matriz_ini[pathAutoload];



	define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main', 'pathAutoload');
	
	if (strpos('@php_bin@', '@php_bin') === 0) {

		//require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
		require  $pathAutoload;
	} else {
		require '@php_dir@' . DIRECTORY_SEPARATOR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
	}
	
	
	
	class PUWI_Runner{
		
		
		
		public static function main($exit = TRUE)
    	{
			$puwi = new PUWI_Runner;
			$command = new PHPUnit_TextUI_Command;
			return $command->run($_SERVER['argv'],$exit);

		}

/*
		public function run (array $argv, $exit = TRUE)
		{
			echo "\n----Run-----\n";
			$command = new PHPUnit_TextUI_Command;
			PHPUnit_TextUI_Command::handleArguments($argv);
			
			
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
			
			try{
				$result = $runner->doRun($suite,$this->arguments);

			}
			catch (PHPUnit_Framework_Exception $e) { print $e->getMessage() . "\n"; }

		}


		protected function createRunner()
		{
			echo "\n----createRunner-----\n";
			return new PHPUnit_TextUI_TestRunner($this->arguments['loader']);
		}
*/

	}
	PUWI_Runner::main();

?>
