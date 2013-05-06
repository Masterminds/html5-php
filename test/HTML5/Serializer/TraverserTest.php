<?php
namespace HTML5\Tests;

use \HTML5\Serializer\Traverser;
use \HTML5\Parser;

require_once __DIR__ . '/../TestCase.php';

class TraverserTest extends \HTML5\Tests\TestCase {

  // Dummy markup to parse then try to traverse. Note, not using any html5
  // so we can use the old parser until ours is complete.
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
    $class = new \ReflectionClass('\HTML5\Serializer\Traverser');
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  function getTraverser() {
    $stream = fopen('php://temp', 'w');

    // Using the existing parser (libxml).
    // @todo switch to the html5 parser.
    $dom = new \DOMDocument();
    $dom->loadHTML($this->markup);

    $t = new Traverser($dom, $stream);

    // We return both the traverser and stream so we can pull from it.
    return array($t, $stream);
  }

  function testConstruct() {

    // The traverser needs a place to write the output to. In our case we
    // use a stream in temp space.
    $stream = fopen('php://temp', 'w');

    // Using the existing parser (libxml).
    // @todo switch to the html5 parser.
    $dom = new \DOMDocument();
    $dom->loadHTML($this->markup);

    $t = new Traverser($dom, $stream);

    $this->assertInstanceOf('\HTML5\Serializer\Traverser', $t);
  }

  function testNl() {
    list($t, $s) = $this->getTraverser();

    $m = $this->getProtectedMethod('nl');
    $m->invoke($t);
    $this->assertEquals(PHP_EOL, stream_get_contents($s, -1, 0));
  }

  function testWr() {
    list($t, $s) = $this->getTraverser();

    $m = $this->getProtectedMethod('wr');
    $m->invoke($t, 'foo');
    $this->assertEquals('foo', stream_get_contents($s, -1, 0));
  }

  function testText() {
    // $dom = new \DOMDocument();
    // $dom->loadHTML('<!doctype html>
    // <html lang="en">
    //   <head>
    //     <meta charset="utf-8">
    //     <title>Test</title>
    //   </head>
    //   <body>
    //     <textarea>baz()</textarea>
    //   </body>
    // </html>');
    // //var_export($dom);
    // print_r($dom);
    // $list = $dom->getElementsByTagName('textarea');
    // print_r($list->item(0));
    //print_r($dom->get());

  }
}