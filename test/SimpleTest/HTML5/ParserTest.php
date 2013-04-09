<?php

require_once dirname(__FILE__) . '/../autorun.php';

class HTML5_ParserTest extends UnitTestCase
{
    public function testParse() {
        $result = HTML5_Parser::parse('<html><body></body></html>');
        $this->assertIsA($result, 'DOMDocument');
    }
    public function testParseFragment() {
        $result = HTML5_Parser::parseFragment('<b>asdf</b> foo');
        $this->assertIsA($result, 'DOMNodeList');
    }
}
