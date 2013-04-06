<?php

namespace HTML5\Tests;

use \HTML5\Serializer;

require_once 'TestCase.php';

class SerializerTest extends TestCase {
  public function testBasicDocument() {
    $html = '<!DOCTYPE html><html><body>test</body></html>';

    $dom = \HTML5::parse($html);

    $this->assertTrue($dom instanceof \DOMDocument, "Canary");

    $ser = new \HTML5\Serializer($dom);

    $out = $ser->saveHTML();

    $this->assertTrue(count($out) >= count($html));

  }
}
