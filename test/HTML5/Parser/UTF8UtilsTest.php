<?php

namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\UTF8Utils;

class UTF8UtilsTest extends \Masterminds\HTML5\Tests\TestCase
{
    public function testConvertToUTF8()
    {
        $out = UTF8Utils::convertToUTF8('éàa', 'ISO-8859-1');
        self::assertEquals('a', $out);
    }

    /**
     * @todo add tests for invalid codepoints
     */
    public function testCheckForIllegalCodepoints()
    {
        $smoke = "Smoke test";
        $err = UTF8Utils::checkForIllegalCodepoints($smoke);
        self::assertEmpty($err);

        $data = "Foo Bar \0 Baz";
        $errors = UTF8Utils::checkForIllegalCodepoints($data);
        self::assertContains('null-character', $errors);
    }
}