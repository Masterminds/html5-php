<?php

SimpleTest::ignore('HTML5_TestDataHarness');
abstract class HTML5_TestDataHarness extends HTML5_DataHarness
{
    protected $data;
    public function __construct() {
        parent::__construct();
        $this->data = new HTML5_TestData($this->filename);
    }
    public function getDescription($test) {
        return $test['data'];
    }
    public function getDataTests() {
        return $this->data->tests;
    }
}

