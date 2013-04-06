<?php
// TODO: Add XML namespace examples.

namespace HTML5\Tests;

use \HTML5\Serializer;

require_once 'TestCase.php';

/**
 * Test the Serializer.
 *
 * These tests are all dependent upon the parser. So if the parser
 * fails, the results of the serializer tests may not be conclusive.
 */
class SerializerTest extends TestCase {

  /**
   * Parse and serialize a string.
   */
  protected function cycle($html) {
    $dom = \HTML5::parse($html);
    $ser = new \HTML5\Serializer($dom, FALSE);
    $out = $ser->saveHTML();

    return $out;
  }

  public function testSaveHTML() {
    $html = '<!DOCTYPE html><html><body>test</body></html>';

    $dom = \HTML5::parse($html);
    $this->assertTrue($dom instanceof \DOMDocument, "Canary");

    $ser = new \HTML5\Serializer($dom, FALSE);
    $out = $ser->saveHTML();

    $this->assertTrue(count($out) >= count($html), 'Byte counts');
    $this->assertRegExp('/<!DOCTYPE html>/', $out, 'Has DOCTYPE.');
    $this->assertRegExp('/<body>test<\/body>/', $out, 'Has body text.');

  }

  public function testSave() {
    $html = '<!DOCTYPE html><html><body>test</body></html>';

    $dom = \HTML5::parse($html);
    $this->assertTrue($dom instanceof \DOMDocument, "Canary");

    $ser = new \HTML5\Serializer($dom, FALSE);
    $out = fopen("php://temp", "w");
    $ser->save($out);

    rewind($out);
    $res = stream_get_contents($out);
    $this->assertTrue(count($res) >= count($html));
  }

  public function testElements() {
    // Should have content.
    $res = $this->cycle('<div>FOO</div>');
    $this->assertRegExp('|<div>FOO</div>|', $res);

    // Should be empty
    $res = $this->cycle('<span></span>');
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
  }

  public function testPCData() {
    $res = $this->cycle('<a>This is a test.</a>');
    $this->assertRegExp('|This is a test.|', $res);

    $res = $this->cycle('This
      is
      a
      test.');

    // Check that newlines are there, but don't count spaces.
    $this->assertRegExp('|This\n\s*is\n\s*a\n\s*test.|', $res);

    $res = $this->cycle('<a>This <em>is</em> a test.</a>');
    $this->assertRegExp('|This <em>is</em> a test.|', $res);
  }

  public function testUnescaped() {
    $res = $this->cycle('<script>2 < 1</script>');
    $this->assertRegExp('|2 < 1|', $res);

    $res = $this->cycle('<style>div>div>div</style>');
    $this->assertRegExp('|div>div>div|', $res);
  }

  public function testEntities() {
    $res = $this->cycle('<a>Apples &amp; bananas.</a>');
    $this->assertRegExp('|Apples &amp; bananas.|', $res);
  }

  public function testComment() {
    $res = $this->cycle('a<!-- This is a test. -->b');
    $this->assertRegExp('|<!-- This is a test. -->|', $res);
  }

  // FAILS because the parser converts CDATA to a comment. Issue #2.
  public function testCDATA() {
    $res = $this->cycle('a<![CDATA[ This <is> a test. ]]>b');
    $this->assertRegExp('|<![CDATA[ This <is> a test. ]]>|', $res);
  }
}
