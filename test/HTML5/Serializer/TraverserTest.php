<?php
namespace HTML5\Tests;

use \HTML5\Serializer\Traverser;
use \HTML5\Parser;

require_once __DIR__ . '/../TestCase.php';

class TraverserTest extends \HTML5\Tests\TestCase {

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

  public function testIsBlock() {
    $blocks = array('html', 'body', 'head', 'p', 'div', 'h1', 'h2', 'h3', 'h4',
      'h5', 'h6', 'title', 'script', 'link', 'meta', 'section', 'article',
      'table', 'tbody', 'tr', 'th', 'td',
      //'form',
    );

    // Mocking the required input because there is no checking.
    $t = new Traverser('', '');
    $method = $this->getProtectedMethod('isBlock');

    foreach ($blocks as $block) {
      $this->assertTrue($method->invoke($t, $block), 'Block test failed on: ' . $block);
    }

    $nonblocks = array('span', 'a', 'img');
    foreach ($nonblocks as $tag) {
      $this->assertFalse($method->invoke($t, $tag),  'Block test failed on: ' . $tag);
    }
  }

  public function testIsUnary() {
    $elements = array( 'area', 'base', 'basefont', 'bgsound', 'br', 'col',
      'command', 'embed', 'frame', 'hr', 'img',
    );

    // Mocking the required input because there is no checking.
    $t = new Traverser('', '');
    $method = $this->getProtectedMethod('isUnary');

    foreach ($elements as $element) {
      $this->assertTrue($method->invoke($t, $element), 'Unary test failed on: ' . $element);
    }

    $nonblocks = array('span', 'a', 'div');
    foreach ($nonblocks as $tag) {
      $this->assertFalse($method->invoke($t, $tag),  'Unary test failed on: ' . $tag);
    }
  }

}