<?php
namespace HTML5\Tests\Serializer;

use \HTML5\Serializer\OutputRules;
use \HTML5\Serializer\Traverser;
use \HTML5\Parser;

class OutputRulesTest extends \HTML5\Tests\TestCase {

  protected $markup = '<!doctype html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <title>Test</title>
      </head>
      <body>
        <p>This is a test.</p>
      </body>
    </html>';
  public function setUp()
  {
      $this->html5 = $this->getInstance();
  }
  /**
   * Using reflection we make a protected method accessible for testing.
   *
   * @param string $name
   *   The name of the method on the Traverser class to test.
   *
   * @return \ReflectionMethod
   *   \ReflectionMethod for the specified method
   */
  function getProtectedMethod($name) {
    $class = new \ReflectionClass('\HTML5\Serializer\OutputRules');
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  function getTraverserProtectedProperty($name) {
    $class = new \ReflectionClass('\HTML5\Serializer\Traverser');
    $property = $class->getProperty($name);
    $property->setAccessible(true);
    return $property;
  }

  function getOutputRules($options = array()) {
    $options = $options + $this->html5->getOptions();
    $stream = fopen('php://temp', 'w');
    $dom = $this->html5->loadHTML($this->markup);
    $r = new OutputRules($stream, $options);
    $t = new Traverser($dom, $stream, $r, $options);

    return array($r, $stream);
  }

  function testDocument() {
    $dom = $this->html5->loadHTML('<!doctype html><html lang="en"><body>foo</body></html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $r->document($dom);
    $expected = '<!DOCTYPE html>' . PHP_EOL . '<html lang="en"><body>foo</body></html>' . PHP_EOL;
    $this->assertEquals($expected, stream_get_contents($stream, -1, 0));
  }

  function testDoctype() {
    $dom = $this->html5->loadHTML('<!doctype html><html lang="en"><body>foo</body></html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $m = $this->getProtectedMethod('doctype');
    $m->invoke($r, 'foo');
    $this->assertEquals("<!DOCTYPE html>" . PHP_EOL, stream_get_contents($stream, -1, 0));
  }

  function testElement() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo" class="bar baz">foo bar baz</div>
        <svg width="150" height="100" viewBox="0 0 3 2">
          <rect width="1" height="2" x="0" fill="#008d46" />
          <rect width="1" height="2" x="1" fill="#ffffff" />
          <rect width="1" height="2" x="2" fill="#d2232c" />
        </svg>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('div');
    $r->element($list->item(0));
    $this->assertEquals('<div id="foo" class="bar baz">foo bar baz</div>', stream_get_contents($stream, -1, 0));
  }

  function testOpenTag() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo" class="bar baz">foo bar baz</div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('div');
    $m = $this->getProtectedMethod('openTag');
    $m->invoke($r, $list->item(0));
    $this->assertEquals('<div id="foo" class="bar baz">', stream_get_contents($stream, -1, 0));
  }

  function testCData() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div><![CDATA[bar]]></div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('div');
    $r->cdata($list->item(0)->childNodes->item(0));
    $this->assertEquals('<![CDATA[bar]]>', stream_get_contents($stream, -1, 0));

    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo"></div>
      </body>
    </html>');


    $dom->getElementById('foo')->appendChild(new \DOMCdataSection("]]>Foo<[![CDATA test ]]>"));

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());
    $list = $dom->getElementsByTagName('div');
    $r->cdata($list->item(0)->childNodes->item(0));

    $this->assertEquals('<![CDATA[]]]]><![CDATA[>Foo<[![CDATA test ]]]]><![CDATA[>]]>', stream_get_contents($stream, -1, 0));
  }

  function testComment() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div><!-- foo --></div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('div');
    $r->comment($list->item(0)->childNodes->item(0));
    $this->assertEquals('<!-- foo -->', stream_get_contents($stream, -1, 0));


    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo"></div>
      </body>
      </html>');
    $dom->getElementById('foo')->appendChild(new \DOMComment('<!-- --> --> Foo -->'));

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('div');
    $r->comment($list->item(0)->childNodes->item(0));

    // Could not find more definitive guidelines on what this should be. Went with
    // what the HTML5 spec says and what \DOMDocument::saveXML() produces.
    $this->assertEquals('<!--<!-- --> --> Foo -->-->', stream_get_contents($stream, -1, 0));
  }

  function testText() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <head>
        <script>baz();</script>
      </head>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('script');
    $r->text($list->item(0)->childNodes->item(0));
    $this->assertEquals('baz();', stream_get_contents($stream, -1, 0));

    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <head id="foo"></head>
    </html>');
    $dom->getElementById('foo')->appendChild(new \DOMText('<script>alert("hi");</script>'));

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $item = $dom->getElementById('foo');
    $r->text($item->firstChild);
    $this->assertEquals('&lt;script&gt;alert("hi");&lt;/script&gt;', stream_get_contents($stream, -1, 0));
  }

  function testNl() {
    list($o, $s) = $this->getOutputRules();

    $m = $this->getProtectedMethod('nl');
    $m->invoke($o);
    $this->assertEquals(PHP_EOL, stream_get_contents($s, -1, 0));
  }

  function testWr() {
    list($o, $s) = $this->getOutputRules();

    $m = $this->getProtectedMethod('wr');
    $m->invoke($o, 'foo');
    $this->assertEquals('foo', stream_get_contents($s, -1, 0));
  }

  function getEncData(){
  	return array(
  	  array(FALSE, '&\'<>"', '&amp;\'&lt;&gt;"', '&amp;&apos;&lt;&gt;&quot;'),
  	  array(FALSE, 'This + is. a < test', 'This + is. a &lt; test', 'This &plus; is&period; a &lt; test'),
  	  array(FALSE, '.+#', '.+#', '&period;&plus;&num;'),

  	  array(TRUE, '.+#\'', '.+#\'', '&period;&plus;&num;&apos;'),
  	  array(TRUE, '&".<', '&amp;&quot;.<', '&amp;&quot;&period;&lt;'),
  	  array(TRUE, '&\'<>"', '&amp;\'<>&quot;', '&amp;&apos;&lt;&gt;&quot;'),
  	  array(TRUE, "\xc2\xa0\"'", '&nbsp;&quot;\'', '&nbsp;&quot;&apos;'),
    );
  }

  /**
   * Test basic encoding of text.
   * @dataProvider getEncData
   */
  function testEnc($isAttribute, $test, $expected, $expectedEncoded) {

    list($o, $s) = $this->getOutputRules();
    $m = $this->getProtectedMethod('enc');

    $this->assertEquals($expected, $m->invoke($o, $test, $isAttribute));

    list($o, $s) = $this->getOutputRules(array('encode_entities' => TRUE));
    $m = $this->getProtectedMethod('enc');
    $this->assertEquals($expectedEncoded, $m->invoke($o, $test, $isAttribute));
  }

  /**
   * Test basic encoding of text.
   * @dataProvider getEncData
   */
  function testEscape($isAttribute, $test, $expected, $expectedEncoded) {

    list($o, $s) = $this->getOutputRules();
    $m = $this->getProtectedMethod('escape');

    $this->assertEquals($expected, $m->invoke($o, $test, $isAttribute));
  }

  function testAttrs() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo" class="bar baz" disabled>foo bar baz</div>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('div');

    $m = $this->getProtectedMethod('attrs');
    $m->invoke($r, $list->item(0));

    $content = stream_get_contents($stream, -1, 0);
    $this->assertEquals(' id="foo" class="bar baz" disabled', $content);
  }

  function testSvg() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo" class="bar baz">foo bar baz</div>
        <svg width="150" height="100" viewBox="0 0 3 2">
          <rect width="1" height="2" x="0" fill="#008d46" />
          <rect width="1" height="2" x="1" fill="#ffffff" />
          <rect width="1" height="2" x="2" fill="#d2232c" />
          <rect id="Bar" x="300" y="100" width="300" height="100" fill="rgb(255,255,0)">
            <animate attributeName="x" attributeType="XML" begin="0s" dur="9s" fill="freeze" from="300" to="0" />
          </rect>
        </svg>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('svg');
    $r->element($list->item(0));
    $contents = stream_get_contents($stream, -1, 0);
    $this->assertRegExp('|<svg width="150" height="100" viewBox="0 0 3 2">|', $contents);
    $this->assertRegExp('|<rect width="1" height="2" x="0" fill="#008d46" />|', $contents);
    $this->assertRegExp('|<rect id="Bar" x="300" y="100" width="300" height="100" fill="rgb\(255,255,0\)">|', $contents);
  }

  function testMath() {
    $dom = $this->html5->loadHTML('<!doctype html>
    <html lang="en">
      <body>
        <div id="foo" class="bar baz">foo bar baz</div>
        <math>
          <mi>x</mi>
          <csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">
            <mo>&PlusMinus;</mo>
          </csymbol>
          <mi>y</mi>
        </math>
      </body>
    </html>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $list = $dom->getElementsByTagName('math');
    $r->element($list->item(0));
    $content = stream_get_contents($stream, -1, 0);
    $this->assertRegExp('|<math>|', $content);
    $this->assertRegExp('|<csymbol definitionURL="http://www.example.com/mathops/multiops.html#plusminus">|', $content);
  }

  function testProcessorInstruction() {
    $dom = $this->html5->loadHTMLFragment('<?foo bar ?>');

    $stream = fopen('php://temp', 'w');
    $r = new OutputRules($stream, $this->html5->getOptions());
    $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

    $r->processorInstruction($dom->firstChild);
    $content = stream_get_contents($stream, -1, 0);
    $this->assertRegExp('|<\?foo bar \?>|', $content);
  }
}
