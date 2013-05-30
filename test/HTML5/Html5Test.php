<?php
namespace HTML5\Tests;

require_once 'TestCase.php';

class Html5Test extends TestCase {

  public function testLoad() {
    $dom = \HTML5::load(__DIR__ . '/Html5Test.html');
    $this->assertInstanceOf('\DOMDocument', $dom);
    $this->assertEmpty($dom->errors);
  }

  public function testLoadHTML() {
    $contents = file_get_contents(__DIR__ . '/Html5Test.html');
    $dom = \HTML5::loadHTML($contents);
    $this->assertInstanceOf('\DOMDocument', $dom);
    $this->assertEmpty($dom->errors);
  }

  // This test reads a document into a dom, turn the dom into a document,
  // then tries to read that document again. This makes sure we are reading,
  // and generating a document that works at a high level.
  public function testItWorks() {
    $dom = \HTML5::load(__DIR__ . '/Html5Test.html');
    $this->assertInstanceOf('\DOMDocument', $dom);
    $this->assertEmpty($dom->errors);

    $saved = \HTML5::saveHTML($dom);

    $dom2 = \HTML5::loadHTML($saved);
    $this->assertInstanceOf('\DOMDocument', $dom2);
    $this->assertEmpty($dom2->errors);
  }

}
