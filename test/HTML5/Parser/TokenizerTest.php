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

  public function parse($string) {
    list($tok, $events) = $this->createTokenizer($string);
    $tok->parse();

    return $events;
  }

  public function testParse() {
    list($tok, $events) = $this->createTokenizer('');

    $tok->parse();
    $e1 = $events->get(0);

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
    $events = $this->parse($str);

    $this->assertEquals(2, $events->depth());
    $e1 = $events->get(0);

    $this->assertEquals('&', $e1['data'][0]);

    // Test with hex charref
    $str = '&#x003c;';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('<', $e1['data'][0]);

    // Test with decimal charref
    $str = '&#38;';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('&', $e1['data'][0]);

    // Test with stand-alone ampersand
    $str = '& ';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('&', $e1['data'][0][0], "Stand-alone &");


  }

  /**
   * @expectedException \HTML5\Parser\EventStackParseError
   */
  public function testBrokenCharacterReference() {
    // Test with broken charref
    $str = '&foo';
    $events = $this->parse($str);
  }
}
