<?php

namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\Normalizer;

class NormalizerTest extends \Masterminds\HTML5\Tests\TestCase
{
    /**
     * The aim of the HtmlNormalizer is to add missing root elements to HTML. It needs to be able
     * to handle badly formatted HTML without throwing an error so this is what we're testing here.
     *
     * @return string[][]
     */
    public function invalidHtmlDataProvider()
    {
        return array(
            array(
                '<head>',
                '<html><head></head><body></body></html>'
            ),
            array(
                '</head>',
                '<html><head></head><body></body></html>'
            ),
            array(
                '<head><meta charset="utf8" /></head>',
                '<html><head><meta charset="utf8" /></head><body></body></html>'
            ),
            array(
                '<meta charset="utf8" /></head>',
                '<html><head></head><body><meta charset="utf8" /></body></html>'
            ),
            array(
                '<meta charset="utf8" />',
                '<html><head></head><body><meta charset="utf8" /></body></html>'
            ),
            array(
                '<body>',
                '<html><head></head><body></body></html>'
            ),
            array(
                '<body>Hi</body>',
                '<html><head></head><body>Hi</body></html>'
            ),
            array(
                'Hi</body>',
                '<html><head></head><body>Hi</body></html>'
            ),
            array(
                'Hi',
                '<html><head></head><body>Hi</body></html>'
            ),
            array(
                '<b',
                '<html><head></head><body><b></body></html>'
            ),
            array(
                '<html>',
                '<html><head></head><body></body></html>'
            ),
            array(
                '<html>Hi</html>',
                '<html><head></head><body>Hi</body></html>'
            ),
            array(
                'Hi</html>',
                '<html><head></head><body>Hi</body></html>'
            ),
            array(
                "  <html>\n  Hi</html>   <body></body>",
                "<html>\n  <head></head><body>  Hi</body></html>   "
            )
        );
    }

    /**
     * @test
     *
     * @param string $input
     * @param string $expectedHtml
     *
     * @dataProvider invalidHtmlDataProvider
     */
    public function renderRepairsBrokenHtml($input, $expectedHtml)
    {
        $parser = new Normalizer;
        $parser->loadHtml($input);

        $this->assertEquals($expectedHtml, $parser->saveHtml());
    }
}
