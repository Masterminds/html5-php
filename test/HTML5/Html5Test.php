<?php
namespace Masterminds\HTML5\Tests;

use Masterminds\HTML5;

class Html5Test extends TestCase
{

    /**
     * @var HTML5
     */
    private $html5;

    public function setUp()
    {
        $this->html5 = $this->getInstance();
    }

    public function testLoadOptions()
    {
        // doc
        $dom = $this->html5->loadHTML($this->wrap('<t:tag/>'), array(
            'implicitNamespaces' => array('t' => 'http://example.com'),
            "xmlNamespaces" => true
        ));
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());
        self::assertFalse($this->html5->hasErrors());

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace("t", "http://example.com");
        self::assertEquals(1, $xpath->query("//t:tag")->length);

        // doc fragment
        $frag = $this->html5->loadHTMLFragment('<t:tag/>', array(
            'implicitNamespaces' => array('t' => 'http://example.com'),
            "xmlNamespaces" => true
        ));
        self::assertInstanceOf('\DOMDocumentFragment', $frag);
        self::assertEmpty($this->html5->getErrors());
        self::assertFalse($this->html5->hasErrors());

        $frag->ownerDocument->appendChild($frag);
        $xpath = new \DOMXPath($frag->ownerDocument);
        $xpath->registerNamespace("t", "http://example.com");
        self::assertEquals(1, $xpath->query("//t:tag", $frag)->length);
    }

    public function testErrors()
    {
        $dom = $this->html5->loadHTML('<xx as>');
        self::assertInstanceOf('\DOMDocument', $dom);

        self::assertNotEmpty($this->html5->getErrors());
        self::assertTrue($this->html5->hasErrors());
    }

    public function testLoad()
    {
        $dom = $this->html5->load(__DIR__ . '/Html5Test.html');
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());
        self::assertFalse($this->html5->hasErrors());

        $file = fopen(__DIR__ . '/Html5Test.html', 'r');
        $dom = $this->html5->load($file);
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());

        $dom = $this->html5->loadHTMLFile(__DIR__ . '/Html5Test.html');
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());
    }

    public function testLoadHTML()
    {
        $contents = file_get_contents(__DIR__ . '/Html5Test.html');
        $dom = $this->html5->loadHTML($contents);
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());
    }

    public function testLoadHTMLWithComments()
    {
        $contents = '<!--[if lte IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]> <!--><html class="no-js" lang="en"><head></head><body><白>lall</白></body><!--<![endif]-->
</html>';

        $dom = $this->html5->loadHTML($contents);
        self::assertInstanceOf('\DOMDocument', $dom);

        $expected = '<!DOCTYPE html>
<!--[if lte IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif]--><!--[if gt IE 8]> <!--><html class="no-js" lang="en"><head></head><body><白>lall</白></body><!--<![endif]--></html>
';
        self::assertEquals(
            str_replace(array("\n", "\r", "\r\n"), "", $expected),
            str_replace(array("\n", "\r", "\r\n"), "", $this->html5->saveHTML($dom))
        );
    }

    public function testLoadHTMLFragment()
    {
        $fragment = '<section id="Foo"><div class="Bar">Baz</div></section>';
        $dom = $this->html5->loadHTMLFragment($fragment);
        self::assertInstanceOf('\DOMDocumentFragment', $dom);
        self::assertEmpty($this->html5->getErrors());
    }

    public function testSaveHTML()
    {
        $dom = $this->html5->load(__DIR__ . '/Html5Test.html');
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());

        $saved = $this->html5->saveHTML($dom);
        self::assertRegExp('|<p class="मोनिच">This is a test. \| iñtërnâtiônàlizætiøn</p>|', $saved);
    }

    public function testSaveHTMLFragment()
    {
        $fragment = '<section id="Foo"><div class="Bar">Baz</div></section>';
        $dom = $this->html5->loadHTMLFragment($fragment);

        $string = $this->html5->saveHTML($dom);
        self::assertEquals($fragment, $string);
    }

    public function testSave()
    {
        $dom = $this->html5->load(__DIR__ . '/Html5Test.html');
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());

        // Test resource
        $file = fopen('php://temp', 'w');
        $this->html5->save($dom, $file);
        $content = stream_get_contents($file, -1, 0);
        self::assertRegExp('|<p class="मोनिच">This is a test. \| iñtërnâtiônàlizætiøn</p>|', $content);

        // Test file
        $tmpfname = tempnam(sys_get_temp_dir(), "html5-php");
        $this->html5->save($dom, $tmpfname);
        $content = file_get_contents($tmpfname);
        self::assertRegExp('|<p class="मोनिच">This is a test. \| iñtërnâtiônàlizætiøn</p>|', $content);
        unlink($tmpfname);
    }

    public function testItWorks()
    {
        $dom = $this->html5->load(__DIR__ . '/Html5Test.html');
        self::assertInstanceOf('\DOMDocument', $dom);
        self::assertEmpty($this->html5->getErrors());

        $saved = $this->html5->saveHTML($dom);

        $dom2 = $this->html5->loadHTML($saved);
        self::assertInstanceOf('\DOMDocument', $dom2);
        self::assertEmpty($this->html5->getErrors());
    }

    public function testConfig()
    {
        $html5 = $this->getInstance();
        $options = $html5->getOptions();
        self::assertEquals(false, $options['encode_entities']);

        $html5 = $this->getInstance(array(
            'foo' => 'bar',
            'encode_entities' => true
        ));
        $options = $html5->getOptions();
        self::assertEquals('bar', $options['foo']);
        self::assertEquals(true, $options['encode_entities']);

        // Need to reset to original so future tests pass as expected.
        // $this->getInstance()->setOption('encode_entities', false);
    }

    // This test reads a document into a dom, turn the dom into a document,
    // then tries to read that document again. This makes sure we are reading,
    // and generating a document that works at a high level.

    public function testSvg()
    {
        $dom = $this->html5->loadHTML(
            '<!doctype html>
      <html lang="en">
        <body>
          <div id="foo" class="bar baz">foo bar baz</div>
          <svg width="150" height="100" viewBox="0 0 3 2">
            <rect width="1" height="2" x="0" fill="#008d46" />
            <rect width="1" height="2" x="1" fill="#ffffff" />
            <rect width="1" height="2" x="2" fill="#d2232c" />
            <text font-family="Verdana" font-size="32">
              <textPath xlink:href="#Foo">
                Test Text.
              </textPath>
            </text>
          </svg>
        </body>
      </html>');

        self::assertEmpty($this->html5->getErrors());

        // Test a mixed case attribute.
        $list = $dom->getElementsByTagName('svg');
        self::assertNotEmpty($list->length);
        $svg = $list->item(0);
        self::assertEquals("0 0 3 2", $svg->getAttribute('viewBox'));
        self::assertFalse($svg->hasAttribute('viewbox'));

        // Test a mixed case tag.
        // Note: getElementsByTagName is not case sensetitive.
        $list = $dom->getElementsByTagName('textPath');
        self::assertNotEmpty($list->length);
        $textPath = $list->item(0);
        self::assertEquals('textPath', $textPath->tagName);
        self::assertNotEquals('textpath', $textPath->tagName);

        $html = $this->html5->saveHTML($dom);
        self::assertRegExp('|<svg width="150" height="100" viewBox="0 0 3 2">|', $html);
        self::assertRegExp('|<rect width="1" height="2" x="0" fill="#008d46" />|', $html);
    }

    public function testMathMl()
    {
        $dom = $this->html5->loadHTML(
            '<!doctype html>
      <html lang="en">
        <body>
          <div id="foo" class="bar baz" definitionURL="http://example.com">foo bar baz</div>
          <math>
            <mi>x</mi>
            <csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">
              <mo>&PlusMinus;</mo>
            </csymbol>
            <mi>y</mi>
          </math>
        </body>
      </html>');

        self::assertEmpty($this->html5->getErrors());
        $list = $dom->getElementsByTagName('math');
        self::assertNotEmpty($list->length);

        $list = $dom->getElementsByTagName('div');
        self::assertNotEmpty($list->length);
        $div = $list->item(0);
        self::assertEquals('http://example.com', $div->getAttribute('definitionurl'));
        self::assertFalse($div->hasAttribute('definitionURL'));
        $list = $dom->getElementsByTagName('csymbol');
        $csymbol = $list->item(0);
        self::assertEquals('http://www.example.com/mathops/multiops.html#plusminus', $csymbol->getAttribute('definitionURL'));
        self::assertFalse($csymbol->hasAttribute('definitionurl'));

        $html = $this->html5->saveHTML($dom);
        self::assertRegExp('|<csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">|', $html);
        self::assertRegExp('|<mi>y</mi>|', $html);
    }

    public function testUnknownElements()
    {
        // The : should not have special handling accourding to section 2.9 of the
        // spec. This is differenant than XML. Since we don't know these elements
        // they are handled as normal elements. Note, to do this is really
        // an invalid example and you should not embed prefixed xml in html5.
        $dom = $this->html5->loadHTMLFragment(
            "<f:rug>
      <f:name>Big rectangle thing</f:name>
      <f:width>40</f:width>
      <f:length>80</f:length>
    </f:rug>
    <sarcasm>um, yeah</sarcasm>");

        self::assertEmpty($this->html5->getErrors());
        $markup = $this->html5->saveHTML($dom);
        self::assertRegExp('|<f:name>Big rectangle thing</f:name>|', $markup);
        self::assertRegExp('|<sarcasm>um, yeah</sarcasm>|', $markup);
    }

    public function testElements()
    {
        // Should have content.
        $res = $this->cycle('<div>FOO</div>');
        self::assertRegExp('|<div>FOO</div>|', $res);

        // Should be empty
        $res = $this->cycle('<span></span>');
        self::assertRegExp('|<span></span>|', $res);

        // Should have content.
        $res = $this->cycleFragment('<div>FOO</div>');
        self::assertRegExp('|<div>FOO</div>|', $res);

        // Should be empty
        $res = $this->cycleFragment('<span></span>');
        self::assertRegExp('|<span></span>|', $res);

        // Elements with dashes and underscores
        $res = $this->cycleFragment('<sp-an></sp-an>');
        self::assertRegExp('|<sp-an></sp-an>|', $res);
        $res = $this->cycleFragment('<sp_an></sp_an>');
        self::assertRegExp('|<sp_an></sp_an>|', $res);

        // Should have no closing tag.
        $res = $this->cycle('<hr>');
        self::assertRegExp('|<hr></body>|', $res);
    }

    /**
     * Parse and serialize a string.
     */
    protected function cycle($html)
    {
        $dom = $this->html5->loadHTML('<!DOCTYPE html><html><body>' . $html . '</body></html>');
        $out = $this->html5->saveHTML($dom);

        return $out;
    }

    protected function cycleFragment($fragment)
    {
        $dom = $this->html5->loadHTMLFragment($fragment);
        $out = $this->html5->saveHTML($dom);

        return $out;
    }

    public function testAttributes()
    {
        $res = $this->cycle('<use xlink:href="#svg-track" xmlns:xlink="http://www.w3.org/1999/xlink"></use>');
        self::assertContains('<use xlink:href="#svg-track" xmlns:xlink="http://www.w3.org/1999/xlink"></use>', $res);

        $res = $this->cycle('<div attr="val">FOO</div>');
        self::assertRegExp('|<div attr="val">FOO</div>|', $res);

        // XXX: Note that spec does NOT require attrs in the same order.
        $res = $this->cycle('<div attr="val" class="even">FOO</div>');
        self::assertRegExp('|<div attr="val" class="even">FOO</div>|', $res);

        $res = $this->cycle('<div xmlns:foo="http://example.com">FOO</div>');
        self::assertRegExp('|<div xmlns:foo="http://example.com">FOO</div>|', $res);

        $res = $this->cycleFragment('<div attr="val">FOO</div>');
        self::assertRegExp('|<div attr="val">FOO</div>|', $res);

        // XXX: Note that spec does NOT require attrs in the same order.
        $res = $this->cycleFragment('<div attr="val" class="even">FOO</div>');
        self::assertRegExp('|<div attr="val" class="even">FOO</div>|', $res);

        $res = $this->cycleFragment('<div xmlns:foo="http://example.com">FOO</div>');
        self::assertRegExp('|<div xmlns:foo="http://example.com">FOO</div>|', $res);
    }

    public function testPCData()
    {
        $res = $this->cycle('<a>This is a test.</a>');
        self::assertRegExp('|This is a test.|', $res);

        $res = $this->cycleFragment('<a>This is a test.</a>');
        self::assertRegExp('|This is a test.|', $res);

        $res = $this->cycle('This
      is
      a
      test.');

        // Check that newlines are there, but don't count spaces.
        self::assertRegExp('|This\n\s*is\n\s*a\n\s*test.|', $res);

        $res = $this->cycleFragment('This
      is
      a
      test.');

        // Check that newlines are there, but don't count spaces.
        self::assertRegExp('|This\n\s*is\n\s*a\n\s*test.|', $res);

        $res = $this->cycle('<a>This <em>is</em> a test.</a>');
        self::assertRegExp('|This <em>is</em> a test.|', $res);

        $res = $this->cycleFragment('<a>This <em>is</em> a test.</a>');
        self::assertRegExp('|This <em>is</em> a test.|', $res);
    }

    public function testUnescaped()
    {
        $res = $this->cycle('<script>2 < 1</script>');
        self::assertRegExp('|2 < 1|', $res);

        $res = $this->cycle('<style>div>div>div</style>');
        self::assertRegExp('|div>div>div|', $res);

        $res = $this->cycleFragment('<script>2 < 1</script>');
        self::assertRegExp('|2 < 1|', $res);

        $res = $this->cycleFragment('<style>div>div>div</style>');
        self::assertRegExp('|div>div>div|', $res);
    }

    public function testEntities()
    {
        $res = $this->cycle('<a>Apples &amp; bananas.</a>');
        self::assertRegExp('|Apples &amp; bananas.|', $res);

        $res = $this->cycleFragment('<a>Apples &amp; bananas.</a>');
        self::assertRegExp('|Apples &amp; bananas.|', $res);

        $res = $this->cycleFragment('<p>R&D</p>');
        self::assertRegExp('|R&amp;D|', $res);
    }

    public function testCaseSensitiveTags()
    {
        $dom = $this->html5->loadHTML(
            '<html><body><Button color="red">Error</Button></body></html>',
            array(
                "xmlNamespaces" => true
            )
        );
        $out = $this->html5->saveHTML($dom);
        self::assertRegExp('|<html><body><Button color="red">Error</Button></body></html>|', $out);
    }

    public function testComment()
    {
        $res = $this->cycle('a<!-- This is a test. -->b');
        self::assertRegExp('|<!-- This is a test. -->|', $res);

        $res = $this->cycleFragment('a<!-- This is a test. -->b');
        self::assertRegExp('|<!-- This is a test. -->|', $res);
    }

    public function testCDATA()
    {
        $res = $this->cycle('a<![CDATA[ This <is> a test. ]]>b');
        self::assertRegExp('|<!\[CDATA\[ This <is> a test\. \]\]>|', $res);

        $res = $this->cycleFragment('a<![CDATA[ This <is> a test. ]]>b');
        self::assertRegExp('|<!\[CDATA\[ This <is> a test\. \]\]>|', $res);
    }
}
