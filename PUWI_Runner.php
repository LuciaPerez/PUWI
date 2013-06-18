<?php

class PUWI_Runner extends PHPUnit_TextUI_TestRunner
{

    public function doRunSingleTest(PHPUnit_Framework_Test $suite,array $argv){
    	$results = new PUWI_GetResults();
    	
    	$mySuite = new PHPUnit_Framework_TestSuite;
    	
    	$test_suite = $suite->tests();
    	$result = array();

    	foreach ($test_suite as $test_case){ 
    		$singleTests=$test_case->tests();
    		foreach ($singleTests as $st){
    			switch ($argv[3]){
    				case "test":
    					if ($this->checkSingleTest($test_case->getName()."::".$st->getName(),$argv[2])){
    						$mySuite->addTest($st);
    					}	
    				break;
    				case "file":
    					if ($test_case->getName() == $argv[2]){
    						$mySuite->addTest($st);
    					}
    				break;
    				
    				case 'group':	
    					$groups_info = $suite->getGroupDetails();
    					$total_groups = $results->getGroups($groups_info,$suite->getGroups());
    					
    					if(in_array($argv[2],array_keys($total_groups))){
		    				foreach($total_groups[$argv[2]] as $single_test){
		    					if ($this->checkSingleTest($test_case->getName()."::".$st->getName(), $single_test)){	
		    						$mySuite->addTest($st);
		    					}
		    				}
    					}
    				break;
    			}
    		}
    		
    	}
    	$result = $this->doRun($mySuite, $argv);
    	return $result;
    }
    
    private function checkSingleTest($test, $required_test){
    	$res = ($test == $required_test) ? true : false;
    	return $res;
    }
    
    /**
     * @param  PHPUnit_Framework_Test $suite
     * @param  array   			$arguments
     * @return PHPUnit_Framework_TestResult
     */
    public function doRun(PHPUnit_Framework_Test $suite, array $arguments = array())
    {

        $result = parent::createTestResult();
        
        $suite->run($result);

        unset($suite);
   
        return $result;
    }

}
