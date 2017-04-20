<?php
/**
 * @file
 * Test the Tree Builder.
 */
namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\DOMTreeBuilder;
use Masterminds\HTML5\Parser\Scanner;
use Masterminds\HTML5\Parser\StringInputStream;
use Masterminds\HTML5\Parser\Tokenizer;

/**
 * These tests are functional, not necessarily unit tests.
 */
class DOMTreeBuilderTest extends \Masterminds\HTML5\Tests\TestCase
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Convenience function for parsing.
     *
     * @param $string
     * @param array $options
     *
     * @return \DOMDocument
     */
    protected function parse($string, array $options = array())
    {
        $treeBuilder = new DOMTreeBuilder(false, $options);
        $input = new StringInputStream($string);
        $scanner = new Scanner($input);
        $parser = new Tokenizer($scanner, $treeBuilder);

        $parser->parse();
        $this->errors = $treeBuilder->getErrors();

        return $treeBuilder->document();
    }

    /**
     * Utility function for parsing a fragment of HTML5.
     */
    protected function parseFragment($string)
    {
        $treeBuilder = new DOMTreeBuilder(true);
        $input = new StringInputStream($string);
        $scanner = new Scanner($input);
        $parser = new Tokenizer($scanner, $treeBuilder);

        $parser->parse();
        $this->errors = $treeBuilder->getErrors();

        return $treeBuilder->fragment();
    }

    public function testDocument()
    {
        $html = "<!DOCTYPE html><html></html>";
        $doc = $this->parse($html);

        self::assertInstanceOf('\DOMDocument', $doc);
        self::assertEquals('html', $doc->documentElement->tagName);
        self::assertEquals('http://www.w3.org/1999/xhtml', $doc->documentElement->namespaceURI);
    }

    public function testStrangeCapitalization()
    {
        $html = "<!doctype html>
        <html>
            <head>
                <Title>Hello, world!</TitlE>
            </head>
            <body>TheBody<script>foo</script></body>
        </html>";
        $doc = $this->parse($html);

        self::assertInstanceOf('\DOMDocument', $doc);
        self::assertEquals('html', $doc->documentElement->tagName);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace("x", "http://www.w3.org/1999/xhtml");

        self::assertEquals("Hello, world!", $xpath->query("//x:title")->item(0)->nodeValue);
        self::assertEquals("foo", $xpath->query("//x:script")->item(0)->nodeValue);
    }

    public function testDocumentWithDisabledNamespaces()
    {
        $html = "<!DOCTYPE html><html></html>";
        $doc = $this->parse($html, array('disable_html_ns' => true));

        self::assertInstanceOf('\DOMDocument', $doc);
        self::assertEquals('html', $doc->documentElement->tagName);
        self::assertNull($doc->documentElement->namespaceURI);
    }

    public function testDocumentWithATargetDocument()
    {
        $targetDom = new \DOMDocument();

        $html = "<!DOCTYPE html><html></html>";
        $doc = $this->parse($html, array('target_document' => $targetDom));

        self::assertInstanceOf('\DOMDocument', $doc);
        self::assertSame($doc, $targetDom);
        self::assertEquals('html', $doc->documentElement->tagName);
    }

    public function testDocumentFakeAttrAbsence()
    {
        $html = "<!DOCTYPE html><html xmlns=\"http://www.w3.org/1999/xhtml\"><body>foo</body></html>";
        $doc = $this->parse($html, array('xmlNamespaces' => true));

        $xp = new \DOMXPath($doc);
        self::assertEquals(0, $xp->query("//@html5-php-fake-id-attribute")->length);
    }

    public function testFragment()
    {
        $html = "<div>test</div><span>test2</span>";
        $doc = $this->parseFragment($html);

        self::assertInstanceOf('\DOMDocumentFragment', $doc);
        self::assertTrue($doc->hasChildNodes());
        self::assertEquals('div', $doc->childNodes->item(0)->tagName);
        self::assertEquals('test', $doc->childNodes->item(0)->textContent);
        self::assertEquals('span', $doc->childNodes->item(1)->tagName);
        self::assertEquals('test2', $doc->childNodes->item(1)->textContent);
    }

    public function testElements()
    {
        $html = "<!DOCTYPE html><html><head><title></title></head><body></body></html>";
        $doc = $this->parse($html);
        $root = $doc->documentElement;

        self::assertEquals('html', $root->tagName);
        self::assertEquals('html', $root->localName);
        self::assertEquals('html', $root->nodeName);

        self::assertEquals(2, $root->childNodes->length);
        $kids = $root->childNodes;

        self::assertEquals('head', $kids->item(0)->tagName);
        self::assertEquals('body', $kids->item(1)->tagName);

        $head = $kids->item(0);
        self::assertEquals(1, $head->childNodes->length);
        self::assertEquals('title', $head->childNodes->item(0)->tagName);
    }

    public function testImplicitNamespaces()
    {
        $dom = $this->parse('<!DOCTYPE html><html><body><a xlink:href="bar">foo</a></body></html>');
        $a = $dom->getElementsByTagName('a')->item(0);
        $attr = $a->getAttributeNode('xlink:href');
        self::assertEquals('http://www.w3.org/1999/xlink', $attr->namespaceURI);

        $dom = $this->parse('<!DOCTYPE html><html><body><a xml:base="bar">foo</a></body></html>');
        $a = $dom->getElementsByTagName('a')->item(0);
        $attr = $a->getAttributeNode('xml:base');
        self::assertEquals('http://www.w3.org/XML/1998/namespace', $attr->namespaceURI);
    }

    public function testCustomImplicitNamespaces()
    {
        $dom = $this->parse('<!DOCTYPE html><html><body><a t:href="bar">foo</a></body></html>', array(
            'implicitNamespaces' => array(
                't' => 'http://www.example.com'
            )
        ));
        $a = $dom->getElementsByTagName('a')->item(0);
        $attr = $a->getAttributeNode('t:href');
        self::assertEquals('http://www.example.com', $attr->namespaceURI);

        $dom = $this->parse('<!DOCTYPE html><html><body><t:a>foo</t:a></body></html>', array(
            'implicitNamespaces' => array(
                't' => 'http://www.example.com'
            )
        ));
        $list = $dom->getElementsByTagNameNS('http://www.example.com', 'a');
        self::assertEquals(1, $list->length);
    }

    public function testXmlNamespaces()
    {
        $dom = $this->parse(
            '<!DOCTYPE html><html>
            <t:body xmlns:t="http://www.example.com">
                <a t:href="bar">foo</a>
            </body>
            <div>foo</div>
          </html>', array(
            'xmlNamespaces' => true
        ));
        $a = $dom->getElementsByTagName('a')->item(0);
        $attr = $a->getAttributeNode('t:href');
        self::assertEquals('http://www.example.com', $attr->namespaceURI);

        $list = $dom->getElementsByTagNameNS('http://www.example.com', 'body');
        self::assertEquals(1, $list->length);
    }

    public function testXmlNamespaceNesting()
    {
        $dom = $this->parse(
            '<!DOCTYPE html><html>
            <body xmlns:x="http://www.prefixed.com" id="body">
                <a id="bar1" xmlns="http://www.prefixed.com/bar1">
                    <b id="bar4" xmlns="http://www.prefixed.com/bar4"><x:prefixed id="prefixed"/></b>
                </a>
                <svg id="svg"></svg>
                <c id="bar2" xmlns="http://www.prefixed.com/bar2"></c>
                <div id="div"></div>
                <d id="bar3"></d>
                <xn:d xmlns:xn="http://www.prefixed.com/xn" xmlns="http://www.prefixed.com/bar5_x" id="bar5"><x id="bar5_x"/></xn:d>
            </body>
          </html>', array(
            'xmlNamespaces' => true
        ));


        self::assertEmpty($this->errors);

        $div = $dom->getElementById('div');
        self::assertEquals('http://www.w3.org/1999/xhtml', $div->namespaceURI);

        $body = $dom->getElementById('body');
        self::assertEquals('http://www.w3.org/1999/xhtml', $body->namespaceURI);

        $bar1 = $dom->getElementById('bar1');
        self::assertEquals('http://www.prefixed.com/bar1', $bar1->namespaceURI);

        $bar2 = $dom->getElementById('bar2');
        self::assertEquals("http://www.prefixed.com/bar2", $bar2->namespaceURI);

        $bar3 = $dom->getElementById('bar3');
        self::assertEquals("http://www.w3.org/1999/xhtml", $bar3->namespaceURI);

        $bar4 = $dom->getElementById('bar4');
        self::assertEquals("http://www.prefixed.com/bar4", $bar4->namespaceURI);

        $svg = $dom->getElementById('svg');
        self::assertEquals("http://www.w3.org/2000/svg", $svg->namespaceURI);

        $prefixed = $dom->getElementById('prefixed');
        self::assertEquals("http://www.prefixed.com", $prefixed->namespaceURI);

        $prefixed = $dom->getElementById('bar5');
        self::assertEquals("http://www.prefixed.com/xn", $prefixed->namespaceURI);

        $prefixed = $dom->getElementById('bar5_x');
        self::assertEquals("http://www.prefixed.com/bar5_x", $prefixed->namespaceURI);
    }

    public function testMoveNonInlineElements()
    {
        $doc = $this->parse('<p>line1<br/><hr/>line2</p>');
        self::assertEquals('<html xmlns="http://www.w3.org/1999/xhtml"><p>line1<br/></p><hr/>line2</html>', $doc->saveXML($doc->documentElement), 'Move non-inline elements outside of inline containers.');

        $doc = $this->parse('<p>line1<div>line2</div></p>');
        self::assertEquals('<html xmlns="http://www.w3.org/1999/xhtml"><p>line1</p><div>line2</div></html>', $doc->saveXML($doc->documentElement), 'Move non-inline elements outside of inline containers.');
    }

    public function testAttributes()
    {
        $html = "<!DOCTYPE html>
      <html>
      <head><title></title></head>
      <body id='a' data-iñtërnâtiônàlizætiøn='ñø' class='b c'></body>
      </html>";
        $doc = $this->parse($html);
        $root = $doc->documentElement;

        $body = $root->getElementsByTagName('body')->item(0);
        self::assertEquals('body', $body->tagName);
        self::assertTrue($body->hasAttributes());
        self::assertEquals('a', $body->getAttribute('id'));
        self::assertEquals('b c', $body->getAttribute('class'));
        self::assertEquals('ñø', $body->getAttribute('data-iñtërnâtiônàlizætiøn'));

        $body2 = $doc->getElementById('a');
        self::assertEquals('body', $body2->tagName);
        self::assertEquals('a', $body2->getAttribute('id'));
    }

    public function testSVGAttributes()
    {
        $html = "<!DOCTYPE html>
      <html><body>
      <svg width='150' viewbox='2'>
      <rect textlength='2'/>
      <animatecolor>foo</animatecolor>
      </svg>
      </body></html>";
        $doc = $this->parse($html);
        $root = $doc->documentElement;

        $svg = $root->getElementsByTagName('svg')->item(0);
        self::assertTrue($svg->hasAttribute('viewBox'));

        $rect = $root->getElementsByTagName('rect')->item(0);
        self::assertTrue($rect->hasAttribute('textLength'));

        $ac = $root->getElementsByTagName('animateColor');
        self::assertEquals(1, $ac->length);
    }

    public function testMathMLAttribute()
    {
        $html = '<!doctype html>
      <html lang="en">
        <body>
          <math>
            <mi>x</mi>
            <csymbol definitionurl="http://www.example.com/mathops/multiops.html#plusminus">
              <mo>&PlusMinus;</mo>
            </csymbol>
            <mi>y</mi>
          </math>
        </body>
      </html>';

        $doc = $this->parse($html);
        $root = $doc->documentElement;

        $csymbol = $root->getElementsByTagName('csymbol')->item(0);
        self::assertTrue($csymbol->hasAttribute('definitionURL'));
    }

    public function testMissingHtmlTag()
    {
        $html = "<!DOCTYPE html><title>test</title>";
        $doc = $this->parse($html);

        self::assertEquals('html', $doc->documentElement->tagName);
        self::assertEquals('title', $doc->documentElement->childNodes->item(0)->tagName);
    }

    public function testComment()
    {
        $html = '<html><!--Hello World.--></html>';

        $doc = $this->parse($html);

        $comment = $doc->documentElement->childNodes->item(0);
        self::assertEquals(XML_COMMENT_NODE, $comment->nodeType);
        self::assertEquals("Hello World.", $comment->data);

        $html = '<!--Hello World.--><html></html>';
        $doc = $this->parse($html);

        $comment = $doc->childNodes->item(1);
        self::assertEquals(XML_COMMENT_NODE, $comment->nodeType);
        self::assertEquals("Hello World.", $comment->data);

        $comment = $doc->childNodes->item(2);
        self::assertEquals(XML_ELEMENT_NODE, $comment->nodeType);
        self::assertEquals("html", $comment->tagName);
    }

    public function testCDATA()
    {
        $html = "<!DOCTYPE html><html><math><![CDATA[test]]></math></html>";
        $doc = $this->parse($html);

        $wrapper = $doc->getElementsByTagName('math')->item(0);
        self::assertEquals(1, $wrapper->childNodes->length);
        $cdata = $wrapper->childNodes->item(0);
        self::assertEquals(XML_CDATA_SECTION_NODE, $cdata->nodeType);
        self::assertEquals('test', $cdata->data);
    }

    public function testText()
    {
        $html = "<!DOCTYPE html><html><head></head><body><math>test</math></body></html>";
        $doc = $this->parse($html);

        $wrapper = $doc->getElementsByTagName('math')->item(0);
        self::assertEquals(1, $wrapper->childNodes->length);
        $data = $wrapper->childNodes->item(0);
        self::assertEquals(XML_TEXT_NODE, $data->nodeType);
        self::assertEquals('test', $data->data);

        // The DomTreeBuilder has special handling for text when in before head mode.
        $html = "<!DOCTYPE html><html>
    Foo<head></head><body></body></html>";
        $doc = $this->parse($html);
        self::assertEquals('Line 0, Col 0: Unexpected text. Ignoring: Foo', $this->errors[0]);
        $headElement = $doc->documentElement->firstChild;
        self::assertEquals('head', $headElement->tagName);
    }

    public function testParseErrors()
    {
        $html = "<!DOCTYPE html><html><math><![CDATA[test";
        $doc = $this->parse($html);

        // We're JUST testing that we can access errors. Actual testing of
        // error messages happen in the Tokenizer's tests.
        self::assertGreaterThan(0, count($this->errors));
        self::assertTrue(is_string($this->errors[0]));
    }

    public function testProcessingInstruction()
    {
        // Test the simple case, which is where PIs are inserted into the DOM.
        $doc = $this->parse('<!DOCTYPE html><html><?foo bar?>');
        self::assertEquals(1, $doc->documentElement->childNodes->length);
        $pi = $doc->documentElement->firstChild;
        self::assertInstanceOf('\DOMProcessingInstruction', $pi);
        self::assertEquals('foo', $pi->nodeName);
        self::assertEquals('bar', $pi->data);

        // Leading xml PIs should be ignored.
        $doc = $this->parse('<?xml version="1.0"?><!DOCTYPE html><html><head></head></html>');

        self::assertEquals(2, $doc->childNodes->length);
        self::assertInstanceOf('\DOMDocumentType', $doc->childNodes->item(0));
        self::assertInstanceOf('\DOMElement', $doc->childNodes->item(1));
    }

    public function testAutocloseP()
    {
        $html = "<!DOCTYPE html><html><body><p><figure></body></html>";
        $doc = $this->parse($html);

        $p = $doc->getElementsByTagName('p')->item(0);
        self::assertEquals(0, $p->childNodes->length);
        self::assertEquals('figure', $p->nextSibling->tagName);
    }

    public function testAutocloseLI()
    {
        $html = '<!doctype html>
      <html lang="en">
        <body>
          <ul><li>Foo<li>Bar<li>Baz</ul>
        </body>
      </html>';

        $doc = $this->parse($html);
        $length = $doc->getElementsByTagName('ul')->item(0)->childNodes->length;
        self::assertEquals(3, $length);
    }

    public function testMathML()
    {
        $html = '<!doctype html>
      <html lang="en">
        <body>
          <math xmlns="http://www.w3.org/1998/Math/MathML">
            <mi>x</mi>
            <csymbol definitionurl="http://www.example.com/mathops/multiops.html#plusminus">
              <mo>&PlusMinus;</mo>
            </csymbol>
            <mi>y</mi>
          </math>
        </body>
      </html>';

        $doc = $this->parse($html);
        $math = $doc->getElementsByTagName('math')->item(0);
        self::assertEquals('math', $math->tagName);
        self::assertEquals('math', $math->nodeName);
        self::assertEquals('math', $math->localName);
        self::assertEquals('http://www.w3.org/1998/Math/MathML', $math->namespaceURI);
    }

    public function testSVG()
    {
        $html = '<!doctype html>
      <html lang="en">
        <body>
          <svg width="150" height="100" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
            <rect width="1" height="2" x="2" fill="#d2232c" />
            <text font-family="Verdana" font-size="32">
              <textpath xlink:href="#Foo">
                Test Text.
              </textPath>
            </text>
          </svg>
        </body>
      </html>';

        $doc = $this->parse($html);
        $svg = $doc->getElementsByTagName('svg')->item(0);
        self::assertEquals('svg', $svg->tagName);
        self::assertEquals('svg', $svg->nodeName);
        self::assertEquals('svg', $svg->localName);
        self::assertEquals('http://www.w3.org/2000/svg', $svg->namespaceURI);

        $textPath = $doc->getElementsByTagName('textPath')->item(0);
        self::assertEquals('textPath', $textPath->tagName);
    }

    public function testNoScript()
    {
        $html = '<!DOCTYPE html><html><head><noscript>No JS</noscript></head></html>';
        $doc = $this->parse($html);
        self::assertEmpty($this->errors);
        $noscript = $doc->getElementsByTagName('noscript')->item(0);
        self::assertEquals('noscript', $noscript->tagName);

        $html = '<!DOCTYPE html><html><body><noscript><p>No JS</p></noscript></body></html>';
        $doc = $this->parse($html);
        self::assertEmpty($this->errors);
        $p = $doc->getElementsByTagName('p')->item(0);
        self::assertEquals('p', $p->tagName);
    }

    /**
     * Regression for issue #13
     */
    public function testRegressionHTMLNoBody()
    {
        $html = '<!DOCTYPE html><html><span id="test">Test</span></html>';
        $doc = $this->parse($html);
        $span = $doc->getElementById('test');

        self::assertEmpty($this->errors);

        self::assertEquals('span', $span->tagName);
        self::assertEquals('Test', $span->textContent);
    }

    public function testInstructionProcessor()
    {
        $string = '<!DOCTYPE html><html><?foo bar ?></html>';

        $treeBuilder = new DOMTreeBuilder();
        $is = new InstructionProcessorMock();
        $treeBuilder->setInstructionProcessor($is);

        $input = new StringInputStream($string);
        $scanner = new Scanner($input);
        $parser = new Tokenizer($scanner, $treeBuilder);

        $parser->parse();
        $dom = $treeBuilder->document();
        $div = $dom->getElementsByTagName('div')->item(0);

        self::assertEquals(1, $is->count);
        self::assertEquals('foo', $is->name);
        self::assertEquals('bar ', $is->data);
        self::assertEquals('div', $div->tagName);
        self::assertEquals('foo', $div->textContent);
    }

    public function testSelectGroupedOptions()
    {
        $html = <<<EOM
<!DOCTYPE html>
<html>
    <head>
        <title>testSelectGroupedOptions</title>
    </head>
    <body>
        <select>
            <optgroup id="first" label="first">
                <option value="foo">foo</option>
                <option value="bar">bar</option>
                <option value="baz">baz</option>
            </optgroup>
            <optgroup id="second" label="second">
                <option value="lorem">lorem</option>
                <option value="ipsum">ipsum</option>
            </optgroup>
         </select>
    </body>
</html>
EOM;
        $dom = $this->parse($html);

        self::assertSame(3, $dom->getElementById('first')->getElementsByTagName('option')->length);
        self::assertSame(2, $dom->getElementById('second')->getElementsByTagName('option')->length);
    }
}
