<?php

/**
 * Modified test-case supertype for running tests that are not
 * test method based, but based off of test data that resides in
 * files.
 */
SimpleTest::ignore('HTML5_DataHarness');
abstract class HTML5_DataHarness extends UnitTestCase
{
    /**
     * Filled in by HTML5_TestData::generateTestCases()
     */
    protected $filename;
    private $tests;
    /**
     * Invoked by the runner, it is the function responsible for executing
     * the test and delivering results.
     * @param $test Some easily usable representation of the test
     */
    abstract public function invoke($test);
    /**
     * Returns a list of tests that can be executed. The list members will
     * be passed to invoke(). Return an iterator if you don't want to load
     * all test into memory
     */
    abstract public function getDataTests();
    /**
     * Returns a description of the test
     */
    abstract public function getDescription($test);
    public function getTests() {
        $this->tests = $this->getDataTests();
        // 1-indexed, to be consistent with Python
        $ret = array();
        for ($i = 1; $i <= count($this->tests); $i++) {
            $ret[] = "test_$i";
        }
        return $ret;
    }
    /**
     * Emulates our test functions
     */
    public function __call($name, $args) {
        list($test, $i) = explode("_", $name);
        $this->invoke($this->tests[$i-1]);
    }
}
