<?php

$GLOBALS['TIME'] = 0.0;

error_reporting(E_ALL | E_STRICT);
$simpletest_location = 'simpletest';
$settings_locations = array(
    dirname(__FILE__) . '/../conf/test-settings.php',
    dirname(__FILE__) . '/../test-settings.php',
);

foreach ($settings_locations as $location) {
    if (file_exists($location)) {
        require $location;
        break;
    }
}

function __autoload($class) {
    require str_replace('_', '/', $class) . '.php';
}
set_include_path(
    get_include_path() . PATH_SEPARATOR .
    dirname(__FILE__) . PATH_SEPARATOR .
    dirname(__FILE__) . '/../library'
);
/**
 * Pretty prints a string with ill-formed characters to a Windows
 * cmd.exe screen.
 */
function str_dump($string) {
    for ($i = 0, $len = strlen($string); $i < $len; $i++) {
        $char = $string[$i];
        $byte = ord($char);
        $echo = $char;
        $spec = '';
        if ($byte <= 0x1F || $byte >= 0x7F) {
            $spec = "\\";
            switch ($byte) {
                case 0x00: $echo = '0'; break;
                case 0x07: $echo = 'a'; break;
                case 0x08: $echo = 'b'; break;
                case 0x09: $echo = 't'; break;
                case 0x10: {$echo = "\n"; $spec = ''; break;}
                case 0x11: $echo = 'v'; break;
                case 0x12: $echo = 'f'; break;
                case 0x13: {$echo = $spec = ''; break;}
                case 0x1B: $echo = 'e'; break;
                default:
                    $echo = 'x' . strtoupper(dechex($byte));
            }
        }
        if ($echo == '\\') $echo = '\\\\';
        echo $spec . $echo;
    }
    echo "\n";
}

/**
 * Pretty prints a token as taken by TreeConstructer->emitToken
 */
function token_dump($token) {
    switch ($token['type']) {
    case HTML5_Tokenizer::DOCTYPE:
        echo "<!doctype ...>\n";
        break;
    case HTML5_Tokenizer::STARTTAG:
        $attr = '';
        foreach ($token['attr'] as $kp) {
            $attr .= ' '.$kp['name'] . '="' . $kp['value'] . '"';
        }
        echo "<{$token['name']}$attr>\n";
        break;
    case HTML5_Tokenizer::ENDTAG:
        echo "</{$token['name']}>\n";
        break;
    case HTML5_Tokenizer::COMMENT:
        echo "<!-- {$token['data']} -->\n";
        break;
    case HTML5_Tokenizer::CHARACTER:
        echo '"'.$token['data'].'"'."\n";
        break;
    case HTML5_Tokenizer::EOF:
        echo "EOF\n";
        break;
    }
}

require_once $simpletest_location . '/autorun.php';

class TimedTextReporter extends TextReporter
{
    public function paintFooter($test_name) {
        parent::paintFooter($test_name);
        echo 'Time: ' . $GLOBALS['TIME'] . "\n";
    }
}

function get_microtime() {
    $microtime = explode(' ', microtime());
    return $microtime[1] . substr($microtime[0], 1); 
}

SimpleTest::prefer(new TimedTextReporter());

// vim: et sw=4 sts=4
