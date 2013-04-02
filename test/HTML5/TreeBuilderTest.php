<?php

require_once dirname(__FILE__) . '/../autorun.php';

SimpleTest::ignore('HTML5_TreeBuilderHarness');
class HTML5_TreeBuilderHarness extends HTML5_TestDataHarness
{
    public function assertIdentical($expect, $actual, $test = array()) {
        $input = $test['data'];
        if (isset($test['document-fragment'])) {
            $input .= "\nFragment: " . $test['document-fragment'];
        }
        parent::assertIdentical($expect, $actual, "Identical expectation failed\nInput:\n$input\n\nExpected:\n$expect\n\nActual:\n$actual\n");
    }
    public function invoke($test) {
        // this is totally the wrong interface to use, but
        // for now we need testing
        $tokenizer = new HTML5_Tokenizer($test['data']);
        $GLOBALS['TIME'] -= get_microtime();
        if (isset($test['document-fragment'])) {
            $tokenizer->parseFragment($test['document-fragment']);
        } else {
            $tokenizer->parse();
        }
        $GLOBALS['TIME'] += get_microtime();
        $this->assertIdentical(
            $test['document'],
            HTML5_TestData::strDom($tokenizer->save()),
            $test
        );
    }
}

HTML5_TestData::generateTestCases(
    'HTML5_TreeBuilderHarness',
    'HTML5_TreeBuilderTestOf',
    'tree-construction', '*.dat'
);

