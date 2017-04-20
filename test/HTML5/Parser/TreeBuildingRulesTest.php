<?php
/**
 * @file
 * Test the Tree Builder's special-case rules.
 */
namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\DOMTreeBuilder;
use Masterminds\HTML5\Parser\Scanner;
use Masterminds\HTML5\Parser\StringInputStream;
use Masterminds\HTML5\Parser\Tokenizer;
use Masterminds\HTML5\Parser\TreeBuildingRules;

/**
 * These tests are functional, not necessarily unit tests.
 */
class TreeBuildingRulesTest extends \Masterminds\HTML5\Tests\TestCase
{

    const HTML_STUB = '<!DOCTYPE html><html><head><title>test</title></head><body>%s</body></html>';

    /**
     * Convenience function for parsing.
     */
    protected function parse($string)
    {
        $treeBuilder = new DOMTreeBuilder();
        $scanner = new Scanner(new StringInputStream($string));
        $parser = new Tokenizer($scanner, $treeBuilder);

        $parser->parse();
        return $treeBuilder->document();
    }

    /**
     * Convenience function for parsing fragments.
     */
    protected function parseFragment($string)
    {
        $events = new DOMTreeBuilder(true);
        $scanner = new Scanner(new StringInputStream($string));
        $parser = new Tokenizer($scanner, $events);

        $parser->parse();
        return $events->fragment();
    }

    public function testTDFragment()
    {

        $frag = $this->parseFragment("<td>This is a test of the HTML5 parser</td>");

        $td = $frag->childNodes->item(0);

        self::assertSame(1, $frag->childNodes->length);
        self::assertSame('td', $td->tagName);
        self::assertSame('This is a test of the HTML5 parser', $td->nodeValue);
    }

    public function testHasRules()
    {
        $doc = new \DOMDocument('1.0');
        $engine = new TreeBuildingRules($doc);

        self::assertTrue($engine->hasRules('li'));
        self::assertFalse($engine->hasRules('imaginary'));
    }

    public function testHandleLI()
    {
        $html = sprintf(self::HTML_STUB, '<ul id="a"><li>test<li>test2</ul><a></a>');
        $doc = $this->parse($html);

        $list = $doc->getElementById('a');

        self::assertEquals(2, $list->childNodes->length);
        foreach ($list->childNodes as $ele) {
            self::assertEquals('li', $ele->tagName);
        }
    }

    public function testHandleDT()
    {
        $html = sprintf(self::HTML_STUB, '<dl id="a"><dt>Hello<dd>Hi</dl><a></a>');
        $doc = $this->parse($html);

        $list = $doc->getElementById('a');

        self::assertEquals(2, $list->childNodes->length);
        self::assertEquals('dt', $list->firstChild->tagName);
        self::assertEquals('dd', $list->lastChild->tagName);
    }

    public function testHandleOptionGroupAndOption()
    {
        $html = sprintf(self::HTML_STUB, '<optgroup id="foo" label="foo" ><option value="foo">bar</option></optgroup>');
        $doc = $this->parse($html);

        $list = $doc->getElementById('foo');

        self::assertEquals(1, $list->childNodes->length);

        $option = $list->childNodes->item(0);
        self::assertEquals('option', $option->tagName);
    }

    public function testTable()
    {
        $html = sprintf(self::HTML_STUB, '<table><thead id="a"><th>foo<td>bar<td>baz');
        $doc = $this->parse($html);

        $list = $doc->getElementById('a');

        self::assertEquals(3, $list->childNodes->length);
        self::assertEquals('th', $list->firstChild->tagName);
        self::assertEquals('td', $list->lastChild->tagName);
    }
}
