<?php 
class PUWI_UtilFilter extends PHPUnit_Util_Filter{
	public static function getFilteredStacktrace(Exception $e, $asString = TRUE)
	{
		$prefix = FALSE;
		$script = realpath($GLOBALS['_SERVER']['SCRIPT_NAME']);
	
		if (defined('__PHPUNIT_PHAR__')) {
			$prefix = 'phar://' . __PHPUNIT_PHAR__ . '/';
		}
		
		if (!defined('PHPUNIT_TESTSUITE')) {
			$blacklist = PHPUnit_Util_GlobalState::phpunitFiles();
		} else {
			$blacklist = array();
		}
		
		if ($asString === TRUE) {
			$filteredStacktrace = '';
		} else {
			$filteredStacktrace = array();
		}
	
		if ($e instanceof PHPUnit_Framework_SyntheticError) {
			$eTrace = $e->getSyntheticTrace();
			$eFile  = $e->getSyntheticFile();
			$eLine  = $e->getSyntheticLine();
		} else {
			if ($e->getPrevious()) {
				$eTrace = $e->getPrevious()->getTrace();
			} else {
				$eTrace = $e->getTrace();
			}
			$eFile  = $e->getFile();
			$eLine  = $e->getLine();
		}
	
		if (!self::frameExists($eTrace, $eFile, $eLine)) {
			array_unshift(
			$eTrace, array('file' => $eFile, 'line' => $eLine)
			);
		}
		$method = new ReflectionMethod('PHPUnit_Util_GlobalState', 'addDirectoryContainingClassToPHPUnitFilesList');
		$method->setAccessible(true);
		$method->invoke(new PHPUnit_Util_GlobalState,'PUWI_Command');
		
		$blacklist = array_merge($blacklist,PHPUnit_Util_GlobalState::phpunitFiles());

		
		foreach ($eTrace as $frame) {
			if (isset($frame['file']) && is_file($frame['file']) &&
					!isset($blacklist[$frame['file']]) &&
					strpos($frame['file'], $prefix) !== 0 &&
					$frame['file'] !== $script) {
				if ($asString === TRUE) {
					$filteredStacktrace .= sprintf(
							"%s:%s\n",
	
							$frame['file'],
							isset($frame['line']) ? $frame['line'] : '?'
					);
				} else {
					$filteredStacktrace[] = $frame;
				}
			}
		}
	
		return $filteredStacktrace;
	}
	
}

?>