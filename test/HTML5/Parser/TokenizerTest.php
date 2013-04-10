<?php
namespace HTML5\Parser;
require __DIR__ . '/../TestCase.php';
require 'EventStack.php';

class TokenizerTest extends \HTML5\Tests\TestCase {
  protected function createTokenizer($string) {
    $eventHandler = new EventStack();
    $stream = new StringInputStream($string);
    $scanner = new Scanner($stream);
    return array(
      new Tokenizer($scanner, $eventHandler),
      $eventHandler,
    );
  }

  public function testParse() {
    list($tok, $events) = $this->createTokenizer('');

    $tok->parse();

    $this->assertEquals(1, $events->Depth());
    $this->assertEquals('eof', $e1['name']);
  }

  public function testWhitespace() {
    $spaces = '    ';
    list($tok, $events) = $this->createTokenizer($spaces);

    $tok->parse();

    $this->assertEquals(2, $events->depth());

    $e1 = $events->get(0);

    $this->assertEquals('text', $e1['name']);
    $this->assertEquals($spaces, $e1['data'][0]);
  }

  public function testCharacterReference() {
    $str = '&amp;';
    list($tok, $events) = $this->createTokenizer($str);

    $tok->parse();
    $this->assertEquals(2, $events->depth());
    $e1 = $events->get(0);

    $this->assertEquals('&', $e1['data'][0]);

    // Test with hex charref
    // Test with decimal charref
    // Test with broken charref
    // Test with stand-alone ampersand
  }
}
