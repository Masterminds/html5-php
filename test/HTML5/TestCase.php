<?php
namespace HTML5\Tests;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class TestCase extends \PHPUnit_Framework_TestCase {
  const DOC_OPEN = '<!DOCTYPE html><html><head><title>test</title></head><body>';
  const DOC_CLOSE = '</body></html>';

  public function testFoo() {
    // Placeholder. Why is PHPUnit emitting warnings about no tests?
  }

  protected function wrap($fragment) {
    return self::DOC_OPEN . $fragment . self::DOC_CLOSE;
  }

}
