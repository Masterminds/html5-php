<?php
namespace HTML5\Tests\Parser;

use HTML5\Parser\UTF8Utils;
use HTML5\Parser\StringInputStream;
use HTML5\Parser\Scanner;
use HTML5\Parser\Tokenizer;

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
      $this->assertEquals($expects, $event['data'], "Event $type should equal " . print_r($expects, TRUE) . ": " . print_r($event, TRUE));
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
        'thisisthetagthatdoesntenditjustgoesonandonmyfriend',
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
      '<!doctype html>' => array('html', 0, NULL, FALSE),
      '<!DocType html>' => array('html', 0, NULL, FALSE),
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

  public function testTagsWithAttributeAndMissingName() {
    $cases = array(
      '<id="top_featured">' => 'id',
      '<color="white">' => 'color',
      "<class='neaktivni_stranka'>" => 'class',
      '<bgcolor="white">' => 'bgcolor',
      '<class="nom">' => 'class',
    );

    foreach($cases as $html => $expected) {
      $events = $this->parse($html);
      $this->assertEventError($events->get(0));
      $this->assertEventError($events->get(1));
      $this->assertEventError($events->get(2));
      $this->assertEventEquals('startTag', $expected, $events->get(3));
      $this->assertEventEquals('eof', NULL, $events->get(4));
    }
  }

  public function testTagNotClosedAfterTagName() {
    $cases = array(
      "<noscript<img>" => array('noscript', 'img'),
      '<center<a>' => array('center', 'a'),
      '<br<br>' => array('br', 'br'),
    );

    foreach($cases as $html => $expected) {
      $events = $this->parse($html);
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('startTag', $expected[0], $events->get(1));
      $this->assertEventEquals('startTag', $expected[1], $events->get(2));
      $this->assertEventEquals('eof', NULL, $events->get(3));
    }

    $events = $this->parse('<span<>02</span>');
    $this->assertEventError($events->get(0));
    $this->assertEventEquals('startTag', 'span', $events->get(1));
    $this->assertEventError($events->get(2));
    $this->assertEventEquals('text', '>02', $events->get(3));
    $this->assertEventEquals('endTag', 'span', $events->get(4));
    $this->assertEventEquals('eof', NULL, $events->get(5));

    $events = $this->parse('<p</p>');
    $this->assertEventError($events->get(0));
    $this->assertEventEquals('startTag', 'p', $events->get(1));
    $this->assertEventEquals('endTag', 'p', $events->get(2));
    $this->assertEventEquals('eof', NULL, $events->get(3));

    $events = $this->parse('<strong><WordPress</strong>');
    $this->assertEventEquals('startTag', 'strong', $events->get(0));
    $this->assertEventError($events->get(1));
    $this->assertEventEquals('startTag', 'wordpress', $events->get(2));
    $this->assertEventEquals('endTag', 'strong', $events->get(3));
    $this->assertEventEquals('eof', NULL, $events->get(4));

    $events = $this->parse('<src=<a>');
    $this->assertEventError($events->get(0));
    $this->assertEventError($events->get(1));
    $this->assertEventError($events->get(2));
    $this->assertEventEquals('startTag', 'src', $events->get(3));
    $this->assertEventEquals('startTag', 'a', $events->get(4));
    $this->assertEventEquals('eof', NULL, $events->get(5));

    $events = $this->parse('<br...<a>');
    $this->assertEventError($events->get(0));
    $this->assertEventEquals('startTag', 'br', $events->get(1));
    $this->assertEventEquals('eof', NULL, $events->get(2));
  }

  public function testIllegalTagNames() {
    $cases = array(
      '<li">' => 'li',
      '<p">' => 'p',
      '<b&nbsp; >' => 'b',
      '<static*all>' => 'static',
      '<h*0720/>' => 'h',
      '<st*ATTRIBUTE />' => 'st',
      '<a-href="http://url.com/">' => 'a',
    );

    foreach($cases as $html => $expected) {
      $events = $this->parse($html);
      $this->assertEventError($events->get(0));
      $this->assertEventEquals('startTag', $expected, $events->get(1));
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
      "<foo bar=\"\nbaz\n\">" => array('foo', array('bar' => "\nbaz\n"), FALSE),
      "<foo bar='baz'>" => array('foo', array('bar' => 'baz'), FALSE),
      '<foo bar="A full sentence.">' => array('foo', array('bar' => 'A full sentence.'), FALSE),
      "<foo a='1' b=\"2\">" => array('foo', array('a' => '1', 'b' => '2'), FALSE),
      "<foo ns:bar='baz'>" => array('foo', array('ns:bar' => 'baz'), FALSE),
      "<foo a='blue&amp;red'>" => array('foo', array('a' => 'blue&red'), FALSE),
      "<foo a='blue&&amp;red'>" => array('foo', array('a' => 'blue&&red'), FALSE),
      "<foo\nbar='baz'\n>" => array('foo', array('bar' => 'baz'), FALSE),
      '<doe a deer>' => array('doe', array('a' => NULL, 'deer' => NULL), FALSE),
      '<foo bar=baz>' => array('foo', array('bar' => 'baz'), FALSE),

      // Updated for 8.1.2.3
      '<foo    bar   =   "baz"      >' => array('foo', array('bar' => 'baz'), FALSE),

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
      '<foo bar=>' => array('foo', array('bar' => NULL), FALSE),
      '<foo bar="oh' => array('foo', array('bar' => 'oh'), FALSE),
      '<foo bar=oh">' => array('foo', array('bar' => 'oh"'), FALSE),

      // these attributes are ignored because of current implementation
      // of method "DOMElement::setAttribute"
      // see issue #23: https://github.com/Masterminds/html5-php/issues/23
      '<foo b"="baz">' => array('foo', array(), FALSE),
      '<foo 2abc="baz">' => array('foo', array(), FALSE),
      '<foo ?="baz">' => array('foo', array(), FALSE),
      '<foo foo?bar="baz">' => array('foo', array(), FALSE),

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
      // character "&" in unquoted attribute shouldn't cause an infinite loop
      '<foo bar=index.php?str=1&amp;id=29>' => array('foo', array('bar' => 'index.php?str=1&id=29'), FALSE),
    );
    foreach ($reallyBad as $test => $expects) {
      $events = $this->parse($test);
      //fprintf(STDOUT, $test . print_r($events, TRUE));
      $this->assertEventError($events->get(0));
      $this->assertEventError($events->get(1));
      //$this->assertEventEquals('startTag', $expects, $events->get(1));
    }

    // Regression: Malformed elements should be detected.
    //  '<foo baz="1" <bar></foo>' => array('foo', array('baz' => '1'), FALSE),
    $events = $this->parse('<foo baz="1" <bar></foo>');
    $this->assertEventError($events->get(0));
    $this->assertEventEquals('startTag', array('foo', array('baz' => '1'), FALSE), $events->get(1));
    $this->assertEventEquals('startTag', array('bar', array(), FALSE), $events->get(2));
    $this->assertEventEquals('endTag', array('foo'), $events->get(3));
  }

  public function testRawText() {
    $good = array(
      '<script>abcd efg hijk lmnop</script>     ' => 'abcd efg hijk lmnop',
      '<script><not/><the/><tag></script>' => '<not/><the/><tag>',
      '<script><<<<<<<<</script>' => '<<<<<<<<',
      '<script>hello</script</script>' => 'hello</script',
      "<script>\nhello</script\n</script>" => "\nhello</script\n",
      '<script>&amp;</script>' => '&amp;',
      '<script><!--not a comment--></script>' => '<!--not a comment-->',
      '<script><![CDATA[not a comment]]></script>' => '<![CDATA[not a comment]]>',
    );
    foreach ($good as $test => $expects) {
      $events = $this->parse($test);
      $this->assertEventEquals('startTag', 'script', $events->get(0));
      $this->assertEventEquals('text', $expects, $events->get(1));
      $this->assertEventEquals('endTag', 'script', $events->get(2));
    }

    $bad = array(
      '<script>&amp;</script' => '&amp;</script',
      '<script>Hello world' => 'Hello world',
    );
    foreach ($bad as $test => $expects) {
      $events = $this->parse($test);
      $this->assertEquals(4, $events->depth(), "Counting events for '$test': " . print_r($events, TRUE));
      $this->assertEventEquals('startTag', 'script', $events->get(0));
      $this->assertEventError($events->get(1));
      $this->assertEventEquals('text', $expects, $events->get(2));
    }

    // Testing case sensitivity
    $events = $this->parse('<TITLE>a test</TITLE>');
    $this->assertEventEquals('startTag', 'title', $events->get(0));
    $this->assertEventEquals('text', 'a test', $events->get(1));
    $this->assertEventEquals('endTag', 'title', $events->get(2));

  }

  public function testText() {

    $events = $this->parse('a<br>b');
    $this->assertEquals(4, $events->depth(), "Events: " . print_r($events, TRUE));
    $this->assertEventEquals('text', 'a', $events->get(0));
    $this->assertEventEquals('startTag', 'br', $events->get(1));
    $this->assertEventEquals('text', 'b', $events->get(2));

    $events = $this->parse('<a>Test</a>');
    $this->assertEquals(4, $events->depth(), "Events: " . print_r($events, TRUE));
    $this->assertEventEquals('startTag', 'a', $events->get(0));
    $this->assertEventEquals('text', 'Test', $events->get(1));
    $this->assertEventEquals('endTag', 'a', $events->get(2));

    $events = $this->parse('a<![CDATA[test]]>b');
    $this->assertEquals(4, $events->depth(), "Events: " . print_r($events, TRUE));
    $this->assertEventEquals('text', 'a', $events->get(0));
    $this->assertEventEquals('cdata', 'test', $events->get(1));
    $this->assertEventEquals('text', 'b', $events->get(2));

    $events = $this->parse('a<!--test-->b');
    $this->assertEquals(4, $events->depth(), "Events: " . print_r($events, TRUE));
    $this->assertEventEquals('text', 'a', $events->get(0));
    $this->assertEventEquals('comment', 'test', $events->get(1));
    $this->assertEventEquals('text', 'b', $events->get(2));

    $events = $this->parse('a&amp;b');
    $this->assertEquals(2, $events->depth(), "Events: " . print_r($events, TRUE));
    $this->assertEventEquals('text', 'a&b', $events->get(0));
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
