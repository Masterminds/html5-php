<?php
namespace HTML5\Parser;
require __DIR__ . '/../TestCase.php';
require 'EventStack.php';

class TokenizerTest extends \HTML5\Tests\TestCase {
  // ================================================================
  // Additional assertions.
  // ================================================================
  /**
   * Tests that an event matches both the event type and the expected value.
   *
   * @param string $type
   *   Expected event type.
   * @param string $expects
   *   The value expected in $event['data'][0].
   */
  public function assertEventEquals($type, $expects, $event) {
    $this->assertEquals($type, $event['name'], "Event $type for " . print_r($event, TRUE));
    if (is_array($expects)) {
      $this->assertEquals($expects, $event['data'], "Event $type should equal $expects: " . print_r($event, TRUE));
    }
    else {
      $this->assertEquals($expects, $event['data'][0], "Event $type should equal $expects: " . print_r($event, TRUE));
    }
  }

  /**
   * Assert that a given event is 'error'.
   */
  public function assertEventError($event) {
    $this->assertEquals('error', $event['name'], "Expected error for event: " . print_r($event, TRUE));
  }

  /**
   * Asserts that all of the tests are good.
   *
   * This loops through a map of tests/expectations and runs a few assertions on each test.
   *
   * Checks:
   * - depth (if depth is > 0)
   * - event name
   * - matches on event 0.
   */
  protected function isAllGood($name, $depth, $tests, $debug = FALSE) {
    foreach ($tests as $try => $expects) {
      if ($debug) {
        fprintf(STDOUT, "%s expects %s\n", $try, print_r($expects, TRUE));
      }
      $e = $this->parse($try);
      if ($depth > 0) {
        $this->assertEquals($depth, $e->depth(), "Expected depth $depth for test $try." . print_r($e, TRUE));
      }
      $this->assertEventEquals($name, $expects, $e->get(0));
    }
  }


  // ================================================================
  // Utility functions.
  // ================================================================

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
    $good = array(
      '&amp;' => '&',
      '&#x0003c;' => '<',
      '&#38;' => '&',
      '&' => '&',
    );
    $this->isAllGood('text', 2, $good);

    // Test with broken charref
    $str = '&foo';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('error', $e1['name']);

    $str = '&#xfoo';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('error', $e1['name']);

    $str = '&#foo';
    $events = $this->parse($str);
    $e1 = $events->get(0);
    $this->assertEquals('error', $e1['name']);

    // FIXME: Once the text processor is done, need to verify that the 
    // tokens are transformed correctly into text.
  }

  public function testBogusComment() {
    $bogus = array(
      '</+this is a bogus comment. +>',
      '<!+this is a bogus comment. !>',
      '<!D OCTYPE foo bar>',
      '<!DOCTYEP foo bar>',
      '<![CADATA[ TEST ]]>',
      '<![CDATA Hello ]]>',
      '<![CDATA[ Hello [[>',
      '<!CDATA[[ test ]]>',
      '<![CDATA[',
      '<![CDATA[hellooooo hello',
      '<? Hello World ?>',
      '<? Hello World',
    );
    foreach ($bogus as $str) {
      $events = $this->parse($str);
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('comment', $str, $events->get(1));
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
    $this->isAllGood('endTag', 2, $succeed);

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
      $this->assertEquals(3, $events->depth());
      // Should have triggered an error.
      $this->assertEventError($events->get(0));
      // Should have tried to parse anyway.
      $this->assertEventEquals('endTag', $result, $events->get(1));
    }

    // BogoComments
    $comments = array(
      '</>' => '</>',
      '</ >' => '</ >',
      '</ a>' => '</ a>',
    );
    foreach ($comments as $test => $result) {
      $events = $this->parse($test);
      $this->assertEquals(3, $events->depth());

      // Should have triggered an error.
      $this->assertEventError($events->get(0));

      // Should have tried to parse anyway.
      $this->assertEventEquals('comment', $result, $events->get(1));
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
      $this->assertEventEquals('comment', $expected, $events->get(0));
    }

    $fail = array(
      '<!-->' => '',
      '<!--Hello' => 'Hello',
      "<!--\0Hello" => UTF8Utils::FFFD . 'Hello',
      '<!--' => '',
    );
    foreach ($fail as $test => $expected) {
      $events = $this->parse($test);
      $this->assertEquals(3, $events->depth());
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('comment', $expected, $events->get(1));
    }

  }

  public function testCDATASection() {
    $good = array(
      '<![CDATA[ This is a test. ]]>' => ' This is a test. ',
      '<![CDATA[CDATA]]>' => 'CDATA',
      '<![CDATA[ ]] > ]]>' => ' ]] > ',
      '<![CDATA[ ]]>' => ' ',
    );
    $this->isAllGood('cdata', 2, $good);
  }

  public function testDoctype() {
    $good = array(
      '<!DOCTYPE html>' => array('html', 0, NULL, FALSE),
      "<!DOCTYPE\nhtml>" => array('html', 0, NULL, FALSE),
      "<!DOCTYPE\fhtml>" => array('html', 0, NULL, FALSE),
      '<!DOCTYPE html PUBLIC "foo bar">' => array('html', EventStack::DOCTYPE_PUBLIC, 'foo bar', FALSE),
      "<!DOCTYPE html PUBLIC 'foo bar'>" => array('html', EventStack::DOCTYPE_PUBLIC, 'foo bar', FALSE),
      '<!DOCTYPE      html      PUBLIC     "foo bar"    >' => array('html', EventStack::DOCTYPE_PUBLIC, 'foo bar', FALSE),
      "<!DOCTYPE html \nPUBLIC\n'foo bar'>" => array('html', EventStack::DOCTYPE_PUBLIC, 'foo bar', FALSE),
      '<!DOCTYPE html SYSTEM "foo bar">' => array('html', EventStack::DOCTYPE_SYSTEM, 'foo bar', FALSE),
      "<!DOCTYPE html SYSTEM 'foo bar'>" => array('html', EventStack::DOCTYPE_SYSTEM, 'foo bar', FALSE),
      '<!DOCTYPE      html      SYSTEM "foo/bar"    >' => array('html', EventStack::DOCTYPE_SYSTEM, 'foo/bar', FALSE),
      "<!DOCTYPE html \nSYSTEM\n'foo bar'>" => array('html', EventStack::DOCTYPE_SYSTEM, 'foo bar', FALSE),
    );
    $this->isAllGood('doctype', 2, $good);

    $bad = array(
      '<!DOCTYPE>' => array(NULL, EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE    >' => array(NULL, EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE  foo' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE foo PUB' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE foo PUB>' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE  foo PUB "Looks good">' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE  foo SYSTME "Looks good"' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),

      // Can't tell whether these are ids or ID types, since the context is chopped.
      '<!DOCTYPE foo PUBLIC' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE  foo PUBLIC>' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE foo SYSTEM' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),
      '<!DOCTYPE  foo SYSTEM>' => array('foo', EventStack::DOCTYPE_NONE, NULL, TRUE),

      '<!DOCTYPE html SYSTEM "foo bar"' => array('html', EventStack::DOCTYPE_SYSTEM, 'foo bar', TRUE),
      '<!DOCTYPE html SYSTEM "foo bar" more stuff>' => array('html', EventStack::DOCTYPE_SYSTEM, 'foo bar', TRUE),
    );
    foreach ($bad as $test => $expects) {
      $events = $this->parse($test);
      //fprintf(STDOUT, $test . PHP_EOL);
      $this->assertEquals(3, $events->depth(), "Counting events for '$test': " . print_r($events, TRUE));
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('doctype', $expects, $events->get(1));
    }
  }

  public function testProcessorInstruction() {
    $good = array(
      '<?hph ?>' => 'hph',
      '<?hph echo "Hello World"; ?>' => array('hph', 'echo "Hello World"; '), 
      "<?hph \necho 'Hello World';\n?>" => array('hph', "echo 'Hello World';\n"), 
    );
    $this->isAllGood('pi', 2, $good);
  }

  /**
   * This tests just simple tags.
   */
  public function testSimpleTags() {
    $open = array(
      '<foo>' => 'foo',
      '<FOO>' => 'foo',
      '<fOO>' => 'foo',
      '<foo >' => 'foo',
      "<foo\n\n\n\n>" => 'foo',
      '<foo:bar>' => 'foo:bar',
    );
    $this->isAllGood('startTag', 2, $open);

    $selfClose= array(
      '<foo/>' => 'foo',
      '<FOO/>' => 'foo',
      '<foo />' => 'foo',
      "<foo\n\n\n\n/>" => 'foo',
      '<foo:bar/>' => 'foo:bar',
    );
    foreach ($selfClose as $test => $expects) {
      $events = $this->parse($test);
      $this->assertEquals(3, $events->depth(), "Counting events for '$test'" . print_r($events, TRUE));
      $this->assertEventEquals('startTag', $expects, $events->get(0));
      $this->assertEventEquals('endTag', $expects, $events->get(1));
    }

    $bad = array(
      '<foo' => 'foo',
      '<foo ' => 'foo',
      '<foo/' => 'foo',
      '<foo /' => 'foo',
    );

    foreach ($bad as $test => $expects) {
      $events = $this->parse($test);
      $this->assertEquals(3, $events->depth(), "Counting events for '$test': " . print_r($events, TRUE));
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('startTag', $expects, $events->get(1));
    }
  }

  /**
   * @depends testCharacterReference
   */
  public function testTagAttributes() {
    // Opening tags.
    $good = array(
      '<foo bar="baz">' => array('foo', array('bar' => 'baz'), FALSE),
      '<foo bar=" baz ">' => array('foo', array('bar' => ' baz '), FALSE),
      "<foo bar='baz'>" => array('foo', array('bar' => 'baz'), FALSE),
      '<foo bar="A full sentence.">' => array('foo', array('bar' => 'A full sentence.'), FALSE),
      "<foo a='1' b=\"2\">" => array('foo', array('a' => '1', 'b' => '2'), FALSE),
      "<foo ns:bar='baz'>" => array('foo', array('ns:bar' => 'baz'), FALSE),
      "<foo a='blue&amp;red'>" => array('foo', array('a' => 'blue&red'), FALSE),
      "<foo a='blue&&amp;red'>" => array('foo', array('a' => 'blue&&red'), FALSE),
      "<foo\nbar='baz'\n>" => array('foo', array('bar' => 'baz'), FALSE),
      '<doe a deer>' => array('doe', array('a' => NULL, 'deer' => NULL), FALSE),
      '<foo bar=baz>' => array('foo', array('bar' => 'baz'), FALSE),

      // The spec allows an unquoted value '/'. This will not be a closing
      // tag.
      '<foo bar=/>' => array('foo', array('bar' => '/'), FALSE),
      '<foo bar=baz/>' => array('foo', array('bar' => 'baz/'), FALSE),
    );
    $this->isAllGood('startTag', 2, $good);

    // Self-closing tags.
    $withEnd = array(
      '<foo bar="baz"/>' => array('foo', array('bar' => 'baz'), TRUE),
      '<foo BAR="baz"/>' => array('foo', array('bar' => 'baz'), TRUE),
      '<foo BAR="BAZ"/>' => array('foo', array('bar' => 'BAZ'), TRUE),
      "<foo a='1' b=\"2\" c=3 d/>" => array('foo', array('a' => '1', 'b' => '2', 'c' => '3', 'd' => NULL), TRUE),
    );
    $this->isAllGood('startTag', 3, $withEnd);

    // Cause a parse error.
    $bad = array(
      // This will emit an entity lookup failure for &red.
      "<foo a='blue&red'>" => array('foo', array('a' => 'blue&red'), FALSE),
      "<foo a='blue&&amp;&red'>" => array('foo', array('a' => 'blue&&&red'), FALSE),
      '<foo b"="baz">' => array('foo', array('b"' => 'baz'), FALSE),
      '<foo bar=>' => array('foo', array('bar' => NULL), FALSE),
      '<foo bar="oh' => array('foo', array('bar' => 'oh'), FALSE),
      '<foo bar=oh">' => array('foo', array('bar' => 'oh"'), FALSE),

    );
    foreach ($bad as $test => $expects) {
      $events = $this->parse($test);
      $this->assertEquals(3, $events->depth(), "Counting events for '$test': " . print_r($events, TRUE));
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('startTag', $expects, $events->get(1));
    }

    // Cause multiple parse errors.
    $reallyBad = array(
      '<foo ="bar">' => array('foo', array('=' => NULL, '"bar"' => NULL), FALSE),
      '<foo////>' => array('foo', array(), TRUE),
      '<foo    bar   =   "baz"      >' => array('foo', array('bar' => NULL,  '=' => NULL, '"baz"' => NULL), FALSE),
    );
    foreach ($reallyBad as $test => $expects) {
      $events = $this->parse($test);
      //fprintf(STDOUT, $test . print_r($events, TRUE));
      $this->assertEventError($events->get(0));
      $this->assertEventError($events->get(1));
      //$this->assertEventEquals('startTag', $expects, $events->get(1));
    }
  }

  public function testText() {
    $good = array(
      'a<br>b',
      '<a>test</a>',
      'a<![[ test ]]>b',
      'a&amp;b',
      'a&b',
      'a& b& c',

    );
    $this->markTestIncomplete("Need tag parsing first.");
  }

  // ================================================================
  // Utility functions.
  // ================================================================
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
}
