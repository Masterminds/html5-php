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

  public function testMissingHtmlTag() {
    $html = "<!DOCTYPE html><title>test</title>";
    $doc = $this->parse($html);

    $this->assertEquals('html', $doc->documentElement->tagName);
    $this->assertEquals('title', $doc->documentElement->childNodes->item(0)->tagName);
  }

  public function testComment() {
    $html = '<html><!--Hello World.--></html>';

    $doc = $this->parse($html);

    $comment = $doc->documentElement->childNodes->item(0);
    $this->assertEquals(XML_COMMENT_NODE, $comment->nodeType);
    $this->assertEquals("Hello World.", $comment->data);


    $html = '<!--Hello World.--><html></html>';
    $doc = $this->parse($html);

    $comment = $doc->childNodes->item(1);
    $this->assertEquals(XML_COMMENT_NODE, $comment->nodeType);
    $this->assertEquals("Hello World.", $comment->data);

    $comment = $doc->childNodes->item(2);
    $this->assertEquals(XML_ELEMENT_NODE, $comment->nodeType);
    $this->assertEquals("html", $comment->tagName);
  }

  public function testCDATA() {
    $html = "<!DOCTYPE html><html><mathml><![CDATA[test]]></mathml></html>";
    $doc = $this->parse($html);

    $wrapper = $doc->getElementsByTagName('mathml')->item(0);
    $this->assertEquals(1, $wrapper->childNodes->length);
    $cdata = $wrapper->childNodes->item(0);
    $this->assertEquals(XML_CDATA_SECTION_NODE, $cdata->nodeType);
    $this->assertEquals('test', $cdata->data);
  }

  public function testText() {
    $html = "<!DOCTYPE html><html><head></head><body><mathml>test</mathml></body></html>";
    $doc = $this->parse($html);

    $wrapper = $doc->getElementsByTagName('mathml')->item(0);
    $this->assertEquals(1, $wrapper->childNodes->length);
    $data = $wrapper->childNodes->item(0);
    $this->assertEquals(XML_TEXT_NODE, $data->nodeType);
    $this->assertEquals('test', $data->data);
  }

  public function testParseErrors() {
    $html = "<!DOCTYPE html><html><mathml><![CDATA[test";
    $doc = $this->parse($html);

    // We're JUST testing that we can access errors. Actual testing of 
    // error messages happen in the Tokenizer's tests.
    $this->assertGreaterThan(0,  count($doc->errors));
    $this->assertTrue(is_string($doc->errors[0]));
  }

  public function testProcessingInstruction() {
    $this->markTestIncomplete("Incomplete.");
  }

  public function testAutocloseP() {
    $html = "<!DOCTYPE html><html><body><p><figure></body></html>";
    $doc = $this->parse($html);

    $p = $doc->getElementsByTagName('p')->item(0);
    $this->assertEquals(0, $p->childNodes->length);
    $this->assertEquals('figure', $p->nextSibling->tagName);
  }

  public function testMathML() {
    $this->markTestIncomplete("Incomplete.");
  }

  public function testSVG() {
    $this->markTestIncomplete("Incomplete.");
  }
}
