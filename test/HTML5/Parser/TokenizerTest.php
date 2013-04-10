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

    $this->assertEquals(0, $events->Depth());
  }
}
