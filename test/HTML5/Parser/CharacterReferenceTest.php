<?php
/**
 * @file
 * Test the Scanner. This requires the InputStream tests are all good.
 */
namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\CharacterReference;

class CharacterReferenceTest extends \Masterminds\HTML5\Tests\TestCase
{

    public function testLookupName()
    {
        for ($i = 0; $i <= 2; $i++) { // keep the loop for quick perf-test
            self::assertEquals('&', CharacterReference::lookupName('amp'));
            self::assertEquals('<', CharacterReference::lookupName('lt'));
            self::assertEquals('>', CharacterReference::lookupName('gt'));
            self::assertEquals('"', CharacterReference::lookupName('quot'));
            self::assertEquals('∌', CharacterReference::lookupName('NotReverseElement'));

            self::assertNull(CharacterReference::lookupName('StinkyCheese'));
        }
    }

    public function testLookupHex()
    {
        self::assertEquals('<', CharacterReference::lookupHex('3c'));
        self::assertEquals('<', CharacterReference::lookupHex('003c'));
        self::assertEquals('&', CharacterReference::lookupHex('26'));
        self::assertEquals('}', CharacterReference::lookupHex('7d'));
        self::assertEquals('Σ', CharacterReference::lookupHex('3A3'));
        self::assertEquals('Σ', CharacterReference::lookupHex('03A3'));
        self::assertEquals('Σ', CharacterReference::lookupHex('3a3'));
        self::assertEquals('Σ', CharacterReference::lookupHex('03a3'));
    }

    public function testLookupDecimal()
    {
        self::assertEquals('&', CharacterReference::lookupDecimal(38));
        self::assertEquals('&', CharacterReference::lookupDecimal('38'));
        self::assertEquals('<', CharacterReference::lookupDecimal(60));
        self::assertEquals('Σ', CharacterReference::lookupDecimal(931));
        self::assertEquals('Σ', CharacterReference::lookupDecimal('0931'));
    }
}
