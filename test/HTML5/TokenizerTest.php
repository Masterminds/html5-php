<?php

require_once dirname(__FILE__) . '/../autorun.php';

SimpleTest::ignore('HTML5_TokenizerHarness');
abstract class HTML5_TokenizerHarness extends HTML5_JSONHarness
{
    public function invoke($test) {
        //echo get_class($this) . ': ' . $test->description ."\n";
        if (!isset($test->contentModelFlags)) {
            $test->contentModelFlags = array('PCDATA');
        }
        if (!isset($test->ignoreErrorOrder)) {
            $test->ignoreErrorOrder = false;
        }
        
        // Get expected result array (and maybe error count).
        $expect = array();
        $expectedErrorCount = 0; // This is only used when ignoreErrorOrder = true.
        foreach ($test->output as $tok) {
            // If we're ignoring error order and this is a parse error, just count.
            if ($test->ignoreErrorOrder && $tok === 'ParseError') {
                $expectedErrorCount++;
            } else {
                // Normalize character tokens from the test
                if ($expect && $tok[0] === 'Character' && $expect[count($expect) - 1][0] === 'Character') {
                    $expect[count($expect) - 1][1] .= $tok[1];
                } else {
                    $expect[] = $tok;
                }
            }
        }
        
        // Run test for each content model flag.
        foreach ($test->contentModelFlags as $flag) {
            $output = $this->tokenize($test, $flag);
            $result = array();
            $resultErrorCount = 0; // This is only used when ignoreErrorOrder = true.
            foreach ($output as $tok) {
                // If we're ignoring error order and this is a parse error, just count.
                if ($test->ignoreErrorOrder && $tok === 'ParseError') {
                    $resultErrorCount++;
                } else {
                    $result[] = $tok;
                }
            }
            $this->assertIdentical($expect, $result,
                'In test "'.str_replace('%', '%%', $test->description).
                '" with content model '.$flag.': %s'
            );
            if ($test->ignoreErrorOrder) {
                $this->assertIdentical($expectedErrorCount, $resultErrorCount,
                    'Wrong error count in test "'.str_replace('%', '%%', $test->description).
                    '" with content model '.$flag.': %s'
                );
            }
            if ($expect != $result || ($test->ignoreErrorOrder && $expectedErrorCount !== $resultErrorCount)) {
                echo "Input: "; str_dump($test->input);
                echo "\nExpected: \n"; echo $this->tokenDump($expect);
                echo "\nActual: \n"; echo $this->tokenDump($result);
                echo "\n";
            }
        }
    }
    private function tokenDump($tokens) {
        $ret = '';
        foreach ($tokens as $i => $token) {
            $ret .= ($i+1).". {$token[0]}: {$token[1]}\n";
        }
        return $ret;
    }
    public function tokenize($test, $flag) {
        $flag = constant("HTML5_Tokenizer::$flag");
        if (!isset($test->lastStartTag)) $test->lastStartTag = null;
        $tokenizer = new HTML5_TestableTokenizer($test->input, $flag, $test->lastStartTag);
        $GLOBALS['TIME'] -= get_microtime();
        $tokenizer->parse();
        $GLOBALS['TIME'] += get_microtime();
        return $tokenizer->outputTokens;
    }
}

// generate test suites for tokenizer
HTML5_TestData::generateTestCases(
    'HTML5_TokenizerHarness',
    'HTML5_TokenizerTestOf',
    'tokenizer', '*.test'
);
