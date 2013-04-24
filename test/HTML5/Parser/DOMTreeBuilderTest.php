<?php
/**
 * @file
 * Test the Tree Builder.
 */
namespace HTML5\Parser;

use HTML5\Elements;

require_once __DIR__ . '/../TestCase.php';

/**
 * These tests are functional, not necessarily unit tests.
 */
class DOMTreeBuilderTest extends \HTML5\Tests\TestCase {

  /**
   * Convenience function for parsing.
   */
  protected function parse($string) {
    $treeBuilder = new DOMTreeBuilder();
    $input = new StringInputStream($string);
    $scanner = new Scanner($input);
    $parser = new Tokenizer($scanner, $treeBuilder);

    $parser->parse();

    return $treeBuilder->document();
  }

  public function testDocument() {
    $html = "<!DOCTYPE html><html></html>";
    $doc = $this->parse($html);

    $this->assertInstanceOf('\DOMDocument', $doc);
    $this->assertEquals('html', $doc->documentElement->tagName);
  }

  public function testElements() {
    $html = "<!DOCTYPE html><html><head><title></title></head><body></body></html>";
    $doc = $this->parse($html);
    $root = $doc->documentElement;

    $this->assertEquals('html', $root->tagName);
    $this->assertEquals('html', $root->localName);
    $this->assertEquals('html', $root->nodeName);

    $this->assertEquals(2, $root->childNodes->length);
    $kids = $root->childNodes;

    $this->assertEquals('head', $kids->item(0)->tagName);
    $this->assertEquals('body', $kids->item(1)->tagName);

    $head = $kids->item(0);
    $this->assertEquals(1, $head->childNodes->length);
    $this->assertEquals('title', $head->childNodes->item(0)->tagName);
  }

  public function testAttributes() {
    $html = "<!DOCTYPE html>
      <html>
      <head><title></title></head>
      <body id='a' class='b c'></body>
      </html>";
    $doc = $this->parse($html);
    $root = $doc->documentElement;

    $body = $root->GetElementsByTagName('body')->item(0);
    $this->assertEquals('body', $body->tagName);
    $this->assertTrue($body->hasAttributes());
    $this->assertEquals('a', $body->getAttribute('id'));
    $this->assertEquals('b c', $body->getAttribute('class'));

    $body2 = $doc->getElementById('a');
    $this->assertEquals('body', $body2->tagName);
    $this->assertEquals('a', $body2->getAttribute('id'));
  }

  public function testComment() {
    $this->markTestIncomplete("Incomplete.");
  }

  public function testCDATA() {
    $this->markTestIncomplete("Incomplete.");
  }

  public function testText() {
    $this->markTestIncomplete("Incomplete.");
  }

  public function testParseErrors() {
    $this->markTestIncomplete("Incomplete.");
  }

  public function testProcessingInstruction() {
    $this->markTestIncomplete("Incomplete.");
  }
}
