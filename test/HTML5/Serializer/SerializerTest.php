<?php
// TODO: Add XML namespace examples.

namespace HTML5\Tests;

use \HTML5\Serializer\Serializer;

require_once __DIR__ . '/../TestCase.php';

/**
 * Test the Serializer.
 *
 * These tests are all dependent upon the parser. So if the parser
 * fails, the results of the serializer tests may not be conclusive.
 */
class SerializerTest extends \HTML5\Tests\TestCase {

  /**
   * Parse and serialize a string.
   */
  protected function cycle($html) {
    $dom = \HTML5::loadHTML('<!DOCTYPE html><html><body>' . $html . '</body></html>');
    $options = \HTML5::options();
    $ser = new Serializer($dom, $options);
    $out = $ser->saveHTML();

    return $out;
  }

  protected function cycleFragment($fragment) {
    $dom = \HTML5::loadHTMLFragment($fragment);
    $options = \HTML5::options();
    $ser = new Serializer($dom, $options);
    $out = $ser->saveHTML();

    return $out;
  }

  public function testSaveHTML() {
    $html = '<!DOCTYPE html><html><body>test</body></html>';

    $dom = \HTML5::loadHTML($html);
    $this->assertTrue($dom instanceof \DOMDocument, "Canary");

    $ser = new Serializer($dom, \HTML5::options());
    $out = $ser->saveHTML();

    $this->assertTrue(count($out) >= count($html), 'Byte counts');
    $this->assertRegExp('/<!DOCTYPE html>/', $out, 'Has DOCTYPE.');
    $this->assertRegExp('/<body>test<\/body>/', $out, 'Has body text.');

  }

  public function testSave() {
    $html = '<!DOCTYPE html><html><body>test</body></html>';

    $dom = \HTML5::loadHTML($html);
    $this->assertTrue($dom instanceof \DOMDocument, "Canary");

    // Test saving to a stream.
    $ser = new Serializer($dom, \HTML5::options());
    $out = fopen("php://temp", "w");
    $ser->save($out);

    rewind($out);
    $res = stream_get_contents($out);
    $this->assertTrue(count($res) >= count($html));

    // Test saving to a file on the file system.
    $tmpfname = tempnam(sys_get_temp_dir(), "html5-php");
    $ser = new Serializer($dom, \HTML5::options());
    $ser->save($tmpfname);
    $content = file_get_contents($tmpfname);
    $this->assertRegExp('|<body>test</body>|', $content);
    unlink($tmpfname);
  }

  public function testElements() {
    // Should have content.
    $res = $this->cycle('<div>FOO</div>');
    $this->assertRegExp('|<div>FOO</div>|', $res);

    // Should be empty
    $res = $this->cycle('<span></span>');
    $this->assertRegExp('|<span></span>|', $res);

    // Should have content.
    $res = $this->cycleFragment('<div>FOO</div>');
    $this->assertRegExp('|<div>FOO</div>|', $res);

    // Should be empty
    $res = $this->cycleFragment('<span></span>');
    $this->assertRegExp('|<span></span>|', $res);

    // Should have no closing tag.
    $res = $this->cycle('<hr>');
    $this->assertRegExp('|<hr></body>|', $res);

  }

  public function testAttributes() {
    $res = $this->cycle('<div attr="val">FOO</div>');
    $this->assertRegExp('|<div attr="val">FOO</div>|', $res);

    // XXX: Note that spec does NOT require attrs in the same order.
    $res = $this->cycle('<div attr="val" class="even">FOO</div>');
    $this->assertRegExp('|<div attr="val" class="even">FOO</div>|', $res);

    $res = $this->cycle('<div xmlns:foo="http://example.com">FOO</div>');
    $this->assertRegExp('|<div xmlns:foo="http://example.com">FOO</div>|', $res);

    $res = $this->cycleFragment('<div attr="val">FOO</div>');
    $this->assertRegExp('|<div attr="val">FOO</div>|', $res);

    // XXX: Note that spec does NOT require attrs in the same order.
    $res = $this->cycleFragment('<div attr="val" class="even">FOO</div>');
    $this->assertRegExp('|<div attr="val" class="even">FOO</div>|', $res);

    $res = $this->cycleFragment('<div xmlns:foo="http://example.com">FOO</div>');
    $this->assertRegExp('|<div xmlns:foo="http://example.com">FOO</div>|', $res);
  }

  public function testPCData() {
    $res = $this->cycle('<a>This is a test.</a>');
    $this->assertRegExp('|This is a test.|', $res);

    $res = $this->cycleFragment('<a>This is a test.</a>');
    $this->assertRegExp('|This is a test.|', $res);

    $res = $this->cycle('This
      is
      a
      test.');

    // Check that newlines are there, but don't count spaces.
    $this->assertRegExp('|This\n\s*is\n\s*a\n\s*test.|', $res);

    $res = $this->cycleFragment('This
      is
      a
      test.');

    // Check that newlines are there, but don't count spaces.
    $this->assertRegExp('|This\n\s*is\n\s*a\n\s*test.|', $res);

    $res = $this->cycle('<a>This <em>is</em> a test.</a>');
    $this->assertRegExp('|This <em>is</em> a test.|', $res);

    $res = $this->cycleFragment('<a>This <em>is</em> a test.</a>');
    $this->assertRegExp('|This <em>is</em> a test.|', $res);
  }

  public function testUnescaped() {
    $res = $this->cycle('<script>2 < 1</script>');
    $this->assertRegExp('|2 < 1|', $res);

    $res = $this->cycle('<style>div>div>div</style>');
    $this->assertRegExp('|div&gt;div&gt;div|', $res);

    $res = $this->cycleFragment('<script>2 < 1</script>');
    $this->assertRegExp('|2 < 1|', $res);

    $res = $this->cycleFragment('<style>div>div>div</style>');
    $this->assertRegExp('|div&gt;div&gt;div|', $res);
  }

  public function testEntities() {
    $res = $this->cycle('<a>Apples &amp; bananas.</a>');
    $this->assertRegExp('|Apples &amp; bananas.|', $res);

    $res = $this->cycleFragment('<a>Apples &amp; bananas.</a>');
    $this->assertRegExp('|Apples &amp; bananas.|', $res);
  }

  public function testComment() {
    $res = $this->cycle('a<!-- This is a test. -->b');
    $this->assertRegExp('|<!-- This is a test. -->|', $res);

    $res = $this->cycleFragment('a<!-- This is a test. -->b');
    $this->assertRegExp('|<!-- This is a test. -->|', $res);
  }

  public function testCDATA() {
    $res = $this->cycle('a<![CDATA[ This <is> a test. ]]>b');
    $this->assertRegExp('|<!\[CDATA\[ This <is> a test\. \]\]>|', $res);

    $res = $this->cycleFragment('a<![CDATA[ This <is> a test. ]]>b');
    $this->assertRegExp('|<!\[CDATA\[ This <is> a test\. \]\]>|', $res);
  }
}
