<?php
namespace HTML5\Tests;

use \HTML5\Parser;

require_once 'TestCase.php';

class ParserTest extends TestCase {
  public function testParse() {
    $result = Parser::parse('<html><body></body></html>');
    $this->assertTrue($result instanceof \DOMDocument);
  }
  public function testParseFragment() {
    $result = Parser::parseFragment('<b>asdf</b> foo');
    $this->assertTrue($result instanceof \DOMNodeList);
  }
}
