<?php
namespace HTML5\Tests;

use \HTML5\Serializer\OutputRules;
use \HTML5\Serializer\Traverser;
use \HTML5\Parser;

require_once __DIR__ . '/../TestCase.php';

class OutputRulesTest extends \HTML5\Tests\TestCase {

  protected $markup = '<!doctype html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <title>Test</title>
      </head>
      <body>
        <p>This is a test.</p>
      </body>
    </html>';

  /**
   * Using reflection we make a protected method accessible for testing.
   * 
   * @param string $name
   *   The name of the method on the Traverser class to test.
   *
   * @return \ReflectionMethod
   *   \ReflectionMethod for the specified method
   */
  function getProtectedMethod($name) {
    $class = new \ReflectionClass('\HTML5\Serializer\OutputRules');
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  function getOutputRules($options = array()) {
    $options = $options + \HTML5::options();
    $stream = fopen('php://temp', 'w');
    $dom = \HTML5::loadHTML($this->markup);
    $t = new Traverser($dom, $stream, $options);

    $o = new OutputRules($t, $stream, $options);

    return array($o, $stream);
  }

  function testDocument() {
    $dom = \HTML5::loadHTML('<!doctype html><html lang="en"><body>foo</body></html>');

    $stream = fopen('php://temp', 'w');
    $t = new Traverser($dom, $stream, \HTML5::options());
    $o = new OutputRules($t, $stream, \HTML5::options());

    $o->document($dom);
    $this->assertEquals("<!DOCTYPE html>\n<html lang=\"en\"><body>foo</body></html>\n", stream_get_contents($stream, -1, 0));
  }


  function testElement() {
    $dom = \HTML5::loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo" class="bar baz">foo bar baz</div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $t = new Traverser($dom, $stream, \HTML5::options());
    $o = new OutputRules($t, $stream, \HTML5::options());

    $list = $dom->getElementsByTagName('div');
    $o->element($list->item(0));
    $this->assertEquals('<div id="foo" class="bar baz">foo bar baz</div>', stream_get_contents($stream, -1, 0));
  }

  function testCData() {
    $dom = \HTML5::loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div><![CDATA[bar]]></div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $t = new Traverser($dom, $stream, \HTML5::options());
    $o = new OutputRules($t, $stream, \HTML5::options());

    $list = $dom->getElementsByTagName('div');
    $o->cdata($list->item(0)->childNodes->item(0));
    $this->assertEquals('<![CDATA[bar]]>', stream_get_contents($stream, -1, 0));
  }

  function testComment() {
    $dom = \HTML5::loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div><!-- foo --></div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $t = new Traverser($dom, $stream, \HTML5::options());
    $o = new OutputRules($t, $stream, \HTML5::options());

    $list = $dom->getElementsByTagName('div');
    $o->comment($list->item(0)->childNodes->item(0));
    $this->assertEquals('<!-- foo -->', stream_get_contents($stream, -1, 0));
  }

  function testText() {
    $dom = \HTML5::loadHTML('<!doctype html>
    <html lang="en">
      <head>
        <script>baz();</script>
      </head>
    </html>');

    $stream = fopen('php://temp', 'w');
    $t = new Traverser($dom, $stream, \HTML5::options());
    $o = new OutputRules($t, $stream, \HTML5::options());

    $list = $dom->getElementsByTagName('script');
    $o->text($list->item(0)->childNodes->item(0));
    $this->assertEquals('baz();', stream_get_contents($stream, -1, 0));
  }

  function testNl() {
    list($o, $s) = $this->getOutputRules();

    $m = $this->getProtectedMethod('nl');
    $m->invoke($o);
    $this->assertEquals(PHP_EOL, stream_get_contents($s, -1, 0));
  }

  function testWr() {
    list($o, $s) = $this->getOutputRules();

    $m = $this->getProtectedMethod('wr');
    $m->invoke($o, 'foo');
    $this->assertEquals('foo', stream_get_contents($s, -1, 0));
  }

  function testEnc() {

    // Test basic escaping of text.
    $tests = array(
      '&\'<>"' => '&amp;&#039;&lt;&gt;&quot;',
      'This + is. a < test' => 'This + is. a &lt; test',
    );

    list($o, $s) = $this->getOutputRules();
    $m = $this->getProtectedMethod('enc');
    foreach ($tests as $test => $expected) {
      $this->assertEquals($expected, $m->invoke($o, $test));
    }

    list($o, $s) = $this->getOutputRules(array('encode' => TRUE));
    $m = $this->getProtectedMethod('enc');

    $this->assertEquals('&period;&plus;&num;', $m->invoke($o, '.+#'));
  }

}