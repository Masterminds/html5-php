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

  public function testSaveHTML() {
    $dom = \HTML5::load(__DIR__ . '/Html5Test.html');
    $this->assertInstanceOf('\DOMDocument', $dom);
    $this->assertEmpty($dom->errors);

    $saved = \HTML5::saveHTML($dom);
    $this->assertRegExp('|<p>This is a test.</p>|', $saved);
  }

  public function testSave() {
    $dom = \HTML5::load(__DIR__ . '/Html5Test.html');
    $this->assertInstanceOf('\DOMDocument', $dom);
    $this->assertEmpty($dom->errors);

    // Test resource
    $file = fopen('php://temp', 'w');
    \HTML5::save($dom, $file);
    $content = stream_get_contents($file, -1, 0);
    $this->assertRegExp('|<p>This is a test.</p>|', $content);

    // Test file
    $tmpfname = tempnam(sys_get_temp_dir(), "html5-php");
    \HTML5::save($dom, $tmpfname);
    $content = file_get_contents($tmpfname);
    $this->assertRegExp('|<p>This is a test.</p>|', $content);
    unlink($tmpfname);
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

  public function testConfig() {
    $options = \HTML5::options();
    $this->assertEquals(FALSE, $options['encode_entities']);
    $this->assertEquals('\HTML5\Serializer\OutputRules', $options['output_rules']);

    \HTML5::setOption('foo', 'bar');
    \HTML5::setOption('encode_entities', TRUE);
    $options = \HTML5::options();
    $this->assertEquals('bar', $options['foo']);
    $this->assertEquals(TRUE, $options['encode_entities']);

    // Need to reset to original so future tests pass as expected.
    \HTML5::setOption('encode_entities', FALSE);
  }

}
