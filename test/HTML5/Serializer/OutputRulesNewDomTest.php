<?php

namespace Masterminds\HTML5\Tests\Serializer;

use Dom\HTMLDocument;
use Masterminds\HTML5\Serializer\OutputRules;
use Masterminds\HTML5\Serializer\Traverser;

class OutputRulesNewDomTest extends AbstractOutputRulesTest
{
    public static function setUpBeforeClass(): void
    {
        if (PHP_VERSION_ID < 80400) {
            self::markTestSkipped('New DOM only supports PHP 8.4+');
        }

        parent::setUpBeforeClass();
    }

    protected function loadHTML($html)
    {
        $options = 0;
        if (defined('Dom\\HTML_NO_DEFAULT_NS')) {
            $options |= constant('Dom\\HTML_NO_DEFAULT_NS');
        }

        return HTMLDocument::createFromString(
            $html,
            $options
        );
    }

    protected function createElementWithText($dom, $name, $value)
    {
        $element = $dom->createElement($name);
        $element->textContent = $value;

        return $element;
    }

    public function testSerializeWithNamespaces()
    {
        $this->html5 = $this->getInstance(array(
            'xmlNamespaces' => true,
        ));

        $source = '
            <!DOCTYPE html>
            <html><head></head><body id="body" xmlns:x="http://www.prefixed.com">
                    <a id="bar1" xmlns="http://www.prefixed.com/bar1">
                        <b id="bar4" xmlns="http://www.prefixed.com/bar4"><x:prefixed id="prefixed">xy</x:prefixed></b>
                    </a>
                    <svg id="svg">svg</svg>
                    <c id="bar2" xmlns="http://www.prefixed.com/bar2"></c>
                    <div id="div"></div>
                    <d id="bar3"></d>
                    <xn:d id="bar5" xmlns:xn="http://www.prefixed.com/xn" xmlns="http://www.prefixed.com/bar5_x"><x id="bar5_x">y</x></xn:d>
                </body></html>';

        $dom = $this->loadHTML($source);

        $stream = fopen('php://temp', 'w');
        $r = new OutputRules($stream, $this->html5->getOptions());
        $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

        $t->walk();
        $rendered = stream_get_contents($stream, -1, 0);

        $clear = function ($s) {
            return trim(preg_replace('/[\s]+/', ' ', $s));
        };

        $this->assertEquals($clear($source), $clear($rendered));
    }

    public function testProcessorInstruction()
    {
        $doc = HTMLDocument::createEmpty();
        $dom = $doc->createProcessingInstruction('foo', 'bar ');

        $stream = fopen('php://temp', 'w');
        $r = new OutputRules($stream, $this->html5->getOptions());
        $t = new Traverser($dom, $stream, $r, $this->html5->getOptions());

        $r->processorInstruction($dom);
        $content = stream_get_contents($stream, -1, 0);
        $this->assertMatchesRegularExpression('|<\?foo bar \?>|', $content);
    }

    public function testHandlingInvalidRawContent()
    {
        self::markTestSkipped('Currently \Dom\HTMLElement will break invalid HTML so skip this test.');
    }
}
