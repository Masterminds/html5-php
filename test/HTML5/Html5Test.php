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

  public function testLoadHTMLFragment() {
    $fragment = '<section id="Foo"><div class="Bar">Baz</div></section>';
    $dom = \HTML5::loadHTMLFragment($fragment);
    $this->assertInstanceOf('\DOMDocumentFragment', $dom);
    $this->assertEmpty($dom->errors);
  }

  public function testSaveHTML() {
    $dom = \HTML5::load(__DIR__ . '/Html5Test.html');
    $this->assertInstanceOf('\DOMDocument', $dom);
    $this->assertEmpty($dom->errors);

    $saved = \HTML5::saveHTML($dom);
    $this->assertRegExp('|<p>This is a test.</p>|', $saved);
  }

  public function testSaveHTMLFragment() {
    $fragment = '<section id="Foo"><div class="Bar">Baz</div></section>';
    $dom = \HTML5::loadHTMLFragment($fragment);

    $string = \HTML5::saveHTML($dom);
    $this->assertEquals($fragment, $string);
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

  public function testSvg() {
    $dom = \HTML5::loadHTML('<!doctype html>
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

    $this->assertEmpty($dom->errors);

    // Test a mixed case attribute.
    $list = $dom->getElementsByTagName('svg');
    $this->assertNotEmpty($list->length);
    $svg = $list->item(0);
    $this->assertEquals("0 0 3 2", $svg->getAttribute('viewBox'));
    $this->assertFalse($svg->hasAttribute('viewbox'));

    // Test a mixed case tag.
    // Note: getElementsByTagName is not case sensetitive.
    $list = $dom->getElementsByTagName('textPath');
    $this->assertNotEmpty($list->length);
    $textPath = $list->item(0);
    $this->assertEquals('textPath', $textPath->tagName);
    $this->assertNotEquals('textpath', $textPath->tagName);

    $html = \HTML5::saveHTML($dom);
    $this->assertRegExp('|<svg width="150" height="100" viewBox="0 0 3 2">|',$html);
    $this->assertRegExp('|<rect width="1" height="2" x="0" fill="#008d46" />|',$html);

  }

  public function testMathMl() {
    $dom = \HTML5::loadHTML('<!doctype html>
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

    $this->assertEmpty($dom->errors);
    $list = $dom->getElementsByTagName('math');
    $this->assertNotEmpty($list->length);

    $list = $dom->getElementsByTagName('div');
    $this->assertNotEmpty($list->length);
    $div = $list->item(0);
    $this->assertEquals('http://example.com', $div->getAttribute('definitionurl'));
    $this->assertFalse($div->hasAttribute('definitionURL'));
    $list = $dom->getElementsByTagName('csymbol');
    $csymbol = $list->item(0);
    $this->assertEquals('http://www.example.com/mathops/multiops.html#plusminus', $csymbol->getAttribute('definitionURL'));
    $this->assertFalse($csymbol->hasAttribute('definitionurl'));

    $html = \HTML5::saveHTML($dom);
    $this->assertRegExp('|<csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">|',$html);
    $this->assertRegExp('|<mi>y</mi>|',$html);
  }

  public function testUnknownElements() {
    
    // The : should not have special handling accourding to section 2.9 of the
    // spec. This is differenant than XML. Since we don't know these elements
    // they are handled as normal elements. Note, to do this is really
    // an invalid example and you should not embed prefixed xml in html5.
    $dom = \HTML5::loadHTMLFragment("<f:rug>
      <f:name>Big rectangle thing</f:name>
      <f:width>40</f:width>
      <f:length>80</f:length>
    </f:rug>");

    $this->assertEmpty($dom->errors);
    $markup = \HTML5::saveHTML($dom);
    $this->assertRegExp('|<f:name>Big rectangle thing</f:name>|',$markup);
  }

}
