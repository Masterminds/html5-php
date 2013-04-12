<?php
namespace HTML5\Parser;
require __DIR__ . '/../TestCase.php';
require 'EventStack.php';

class TokenizerTest extends \HTML5\Tests\TestCase {
  protected function createTokenizer($string, $debug = FALSE) {
    $eventHandler = new EventStack();
    $stream = new StringInputStream($string);
    $scanner = new Scanner($stream);

    $scanner->debug = $debug;

    return array(
      new Tokenizer($scanner, $eventHandler),
      $eventHandler,
    );
  }

  public function parse($string, $debug = FALSE) {
    list($tok, $events) = $this->createTokenizer($string, $debug);
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

  public function testBrokenCharacterReference() {
    // Test with broken charref
    $str = '&foo';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('error', $e1['name']);
  }

  public function testBogusComment() {
    $bogus = array(
      '</+this is a bogus comment. +>',
      '<!+this is a bogus comment. !>',
    );
    foreach ($bogus as $str) {
      $events = $this->parse($str . '   ');
      $e0 = $events->get(0);
      $this->assertEquals('error', $e0['name']);
      $e1 = $events->get(1);
      $this->assertEquals('comment', $e1['name']);
      $this->assertEquals($str, $e1['data'][0]);
    }
  }

  public function testEndTag() {
    $succeed = array(
      '</a>' => 'a',
      '</test>' => 'test',
      '</test 
      >' => 'test',
      '</thisIsTheTagThatDoesntEndItJustGoesOnAndOnMyFriend>' =>
        'thisIsTheTagThatDoesntEndItJustGoesOnAndOnMyFriend',
      // See 8.2.4.10, which requires this and does not say error.
      '</a<b>' => 'a<b', 
    );
    foreach ($succeed as $test => $result) {
      $events = $this->parse($test);
      $this->assertEquals(2, $events->depth());
      $e1 = $events->get(0);
      $this->assertEquals('endTag', $e1['name'], "Parsing $test expects $result.");
      $this->assertEquals($result, $e1['data'][0], "Parse end tag " . $test);
    }


    // Recoverable failures
    $fail = array(
      '</a class="monkey">' => 'a',
      '</a <b>' => 'a',
      '</a <b <c>' => 'a',
      '</a is the loneliest letter>' => 'a',
      '</a' => 'a',
    );
    foreach ($fail as $test => $result) {
      $events = $this->parse($test);
      // Should have triggered an error.
      $e0 = $events->get(0);
      $this->assertEquals('error', $e0['name'], "Parsing $test expects a leading error." . print_r($events, TRUE));
      // Should have tried to parse anyway.
      $e1 = $events->get(1);
      $this->assertEquals('endTag', $e1['name'], "Parsing $test expects resolution to $result." . print_r($events, TRUE));
      $this->assertEquals($result, $e1['data'][0], "Parse end tag " . $test);
      $this->assertEquals(3, $events->depth());
    }

    // BogoComments
    $comments = array(
      '</>' => '</>',
      '</ >' => '</ >',
      '</ a>' => '</ a>',
    );
    foreach ($comments as $test => $result) {
      $events = $this->parse($test);
      // Should have triggered an error.
      $e0 = $events->get(0);
      $this->assertEquals('error', $e0['name'], "Parsing $test expects a leading error." . print_r($events, TRUE));
      // Should have tried to parse anyway.
      $e1 = $events->get(1);
      $this->assertEquals('comment', $e1['name'], "Parsing $test expects comment." . print_r($events, TRUE));
      $this->assertEquals($result, $e1['data'][0], "Parse end tag " . $test);
      $this->assertEquals(3, $events->depth());
    }
  }

  public function testComment() {
    $good = array(
      '<!--easy-->' => 'easy',
      '<!-- 1 > 0 -->' => ' 1 > 0 ',
      '<!-- --$i -->' => ' --$i ',
      '<!----$i-->' => '--$i',
      '<!-- 1 > 0 -->' => ' 1 > 0 ',
      "<!--\nHello World.\na-->" => "\nHello World.\na",
      '<!-- <!-- -->' => ' <!-- ',
    );
    foreach ($good as $test => $expected) {
      $events = $this->parse($test);
      $e1 = $events->get(0);
      $this->assertEquals('comment', $e1['name'], 'Expected a comment for ' . $test);
      $this->assertEquals($expected, $e1['data'][0]);
    }

    $fail = array(
      '<!-->' => '',
      '<!--Hello' => 'Hello',
      "<!--\0Hello" => UTF8Utils::FFFD . 'Hello',
    );
    foreach ($fail as $test => $expected) {
      $events = $this->parse($test);
      $e0 = $events->get(0);
      $this->assertEquals('error', $e0['name'], 'Expected an error for ' . $test . print_r($events, TRUE));

      $e1 = $events->get(1);
      $this->assertEquals('comment', $e1['name'], 'Expected a comment for ' . $test);
      $this->assertEquals($expected, $e1['data'][0]);
    }

  }
}
