<?php
namespace HTML5\Tests;

use \HTML5\Serializer\Traverser;

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

      // Also test the uppercase version.
      $this->assertTrue($method->invoke($t, strtoupper($block)), 'Block test failed on: ' . $block);
    }

    $nonblocks = array('span', 'a', 'img');
    foreach ($nonblocks as $tag) {
      $this->assertFalse($method->invoke($t, $tag),  'Block test failed on: ' . $tag);

      // Also test the uppercase version.
      $this->assertFalse($method->invoke($t, strtoupper($tag)),  'Block test failed on: ' . $tag);
    }
  }

}