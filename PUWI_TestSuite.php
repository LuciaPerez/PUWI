<?php

class PUWI_TestSuite extends PHPUnit_Framework_TestSuite
{

    public function __construct(PHPUnit_Framework_TestSuite $suite_to_copy_from) {
        $this->backupGlobals = $suite_to_copy_from->backupGlobals;
        $this->backupStaticAttributes = $suite_to_copy_from->backupStaticAttributes;
        $this->name = $suite_to_copy_from->name;
        $this->groups = $suite_to_copy_from->groups;
        $this->tests = $suite_to_copy_from->tests;
        $this->numTests = $suite_to_copy_from->numTests;
        $this->testCase = $suite_to_copy_from->testCase;
    }

    public function getGroupDetails() {
        return $this->groups;
    }

}
