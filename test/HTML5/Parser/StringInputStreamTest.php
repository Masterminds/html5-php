<?php
namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\StringInputStream;

class StringInputStreamTest extends \Masterminds\HTML5\Tests\TestCase
{

    /**
     * A canary test to make sure the basics are setup and working.
     */
    public function testConstruct()
    {
        $s = new StringInputStream("abc");

        self::assertInstanceOf('\Masterminds\HTML5\Parser\StringInputStream', $s);
    }

    public function testNext()
    {
        $s = new StringInputStream("abc");

        $s->next();
        self::assertEquals('b', $s->current());
        $s->next();
        self::assertEquals('c', $s->current());
    }

    public function testKey()
    {
        $s = new StringInputStream("abc");

        self::assertEquals(0, $s->key());

        $s->next();
        self::assertEquals(1, $s->key());
    }

    public function testPeek()
    {
        $s = new StringInputStream("abc");

        self::assertEquals('b', $s->peek());

        $s->next();
        self::assertEquals('c', $s->peek());
    }

    public function testCurrent()
    {
        $s = new StringInputStream("abc");

        // Before scanning the string begins the current is empty.
        self::assertEquals('a', $s->current());

        $s->next();
        self::assertEquals('b', $s->current());

        // Test movement through the string.
        $s->next();
        self::assertEquals('c', $s->current());
    }

    public function testColumnOffset()
    {
        $s = new StringInputStream("abc\ndef\n");
        self::assertEquals(0, $s->columnOffset());
        $s->next();
        self::assertEquals(1, $s->columnOffset());
        $s->next();
        self::assertEquals(2, $s->columnOffset());
        $s->next();
        self::assertEquals(3, $s->columnOffset());
        $s->next(); // LF
        self::assertEquals(0, $s->columnOffset());
        $s->next();
        $canary = $s->current(); // e
        self::assertEquals('e', $canary);
        self::assertEquals(1, $s->columnOffset());

        $s = new StringInputStream("abc");
        self::assertEquals(0, $s->columnOffset());
        $s->next();
        self::assertEquals(1, $s->columnOffset());
        $s->next();
        self::assertEquals(2, $s->columnOffset());
    }

    public function testCurrentLine()
    {
        $txt = "1\n2\n\n\n\n3";
        $stream = new StringInputStream($txt);
        self::assertEquals(1, $stream->currentLine());

        // Advance over 1 and LF on to line 2 value 2.
        $stream->next();
        $stream->next();
        $canary = $stream->current();
        self::assertEquals(2, $stream->currentLine());
        self::assertEquals('2', $canary);

        // Advance over 4x LF
        $stream->next();
        $stream->next();
        $stream->next();
        $stream->next();
        $stream->next();
        self::assertEquals(6, $stream->currentLine());
        self::assertEquals('3', $stream->current());

        // Make sure it doesn't do 7.
        self::assertEquals(6, $stream->currentLine());
    }

    public function testRemainingChars()
    {
        $text = "abcd";
        $s = new StringInputStream($text);
        self::assertEquals($text, $s->remainingChars());

        $text = "abcd";
        $s = new StringInputStream($text);
        $s->next(); // Pop one.
        self::assertEquals('bcd', $s->remainingChars());
    }

    public function testCharsUnitl()
    {
        $text = "abcdefffffffghi";
        $s = new StringInputStream($text);
        self::assertEquals('', $s->charsUntil('a'));
        // Pointer at 'a', moves 2 to 'c'
        self::assertEquals('ab', $s->charsUntil('w', 2));

        // Pointer at 'c', moves to first 'f'
        self::assertEquals('cde', $s->charsUntil('fzxv'));

        // Only get five 'f's
        self::assertEquals('fffff', $s->charsUntil('g', 5));

        // Get just the last two 'f's
        self::assertEquals('ff', $s->charsUntil('g'));

        // This should scan to the end.
        self::assertEquals('ghi', $s->charsUntil('w', 9));
    }

    public function testCharsWhile()
    {
        $text = "abcdefffffffghi";
        $s = new StringInputStream($text);

        self::assertEquals('ab', $s->charsWhile('ba'));

        self::assertEquals('', $s->charsWhile('a'));
        self::assertEquals('cde', $s->charsWhile('cdeba'));
        self::assertEquals('ff', $s->charsWhile('f', 2));
        self::assertEquals('fffff', $s->charsWhile('f'));
        self::assertEquals('g', $s->charsWhile('fg'));
        self::assertEquals('hi', $s->charsWhile('fghi', 99));
    }

    public function testBOM()
    {
        // Ignore in-text BOM.
        $stream = new StringInputStream("a\xEF\xBB\xBF");
        self::assertEquals("a\xEF\xBB\xBF", $stream->remainingChars(), 'A non-leading U+FEFF (BOM/ZWNBSP) should remain');

        // Strip leading BOM
        $leading = new StringInputStream("\xEF\xBB\xBFa");
        self::assertEquals('a', $leading->current(), 'BOM should be stripped');
    }

    public function testCarriageReturn()
    {
        // Replace NULL with Unicode replacement.
        $stream = new StringInputStream("\0\0\0");
        self::assertEquals("", $stream->remainingChars(), 'Null character should be replaced by U+FFFD');
        self::assertEquals(0, count($stream->errors), 'Null character should set parse error: ' . print_r($stream->errors, true));

        // Remove CR when next to LF.
        $stream = new StringInputStream("\r\n");
        self::assertEquals("\n", $stream->remainingChars(), 'CRLF should be replaced by LF');

        // Convert CR to LF when on its own.
        $stream = new StringInputStream("\r");
        self::assertEquals("\n", $stream->remainingChars(), 'CR should be replaced by LF');
    }

    public function invalidParseErrorTestHandler($input, $numErrors, $name, $value = '')
    {
        $stream = new StringInputStream($input, 'UTF-8');
        self::assertEquals($value, $stream->remainingChars(), $name . ' (stream content)');
        self::assertEquals($numErrors, count($stream->errors), $name . ' (number of errors)');
    }

    public function testInvalidParseError()
    {
        // C0 controls (except U+0000 and U+000D due to different handling)
        $this->invalidParseErrorTestHandler("\x01", 0, 'U+0001 (C0 control)');
        $this->invalidParseErrorTestHandler("\x02", 0, 'U+0002 (C0 control)');
        $this->invalidParseErrorTestHandler("\x03", 0, 'U+0003 (C0 control)');
        $this->invalidParseErrorTestHandler("\x04", 0, 'U+0004 (C0 control)');
        $this->invalidParseErrorTestHandler("\x05", 0, 'U+0005 (C0 control)');
        $this->invalidParseErrorTestHandler("\x06", 0, 'U+0006 (C0 control)');
        $this->invalidParseErrorTestHandler("\x07", 0, 'U+0007 (C0 control)');
        $this->invalidParseErrorTestHandler("\x08", 0, 'U+0008 (C0 control)');
        $this->invalidParseErrorTestHandler("\x09", 0, 'U+0009 (C0 control)', "	"); // space
        $this->invalidParseErrorTestHandler("\x0A", 0, 'U+000A (C0 control)', "\x0A"); // line-break
        $this->invalidParseErrorTestHandler("\x0B", 0, 'U+000B (C0 control)');
        $this->invalidParseErrorTestHandler("\x0C", 0, 'U+000C (C0 control)');
        $this->invalidParseErrorTestHandler("\x0E", 0, 'U+000E (C0 control)');
        $this->invalidParseErrorTestHandler("\x0F", 0, 'U+000F (C0 control)');
        $this->invalidParseErrorTestHandler("\x10", 0, 'U+0010 (C0 control)');
        $this->invalidParseErrorTestHandler("\x11", 0, 'U+0011 (C0 control)');
        $this->invalidParseErrorTestHandler("\x12", 0, 'U+0012 (C0 control)');
        $this->invalidParseErrorTestHandler("\x13", 0, 'U+0013 (C0 control)');
        $this->invalidParseErrorTestHandler("\x14", 0, 'U+0014 (C0 control)');
        $this->invalidParseErrorTestHandler("\x15", 0, 'U+0015 (C0 control)');
        $this->invalidParseErrorTestHandler("\x16", 0, 'U+0016 (C0 control)');
        $this->invalidParseErrorTestHandler("\x17", 0, 'U+0017 (C0 control)');
        $this->invalidParseErrorTestHandler("\x18", 0, 'U+0018 (C0 control)');
        $this->invalidParseErrorTestHandler("\x19", 0, 'U+0019 (C0 control)');
        $this->invalidParseErrorTestHandler("\x1A", 0, 'U+001A (C0 control)');
        $this->invalidParseErrorTestHandler("\x1B", 0, 'U+001B (C0 control)');
        $this->invalidParseErrorTestHandler("\x1C", 0, 'U+001C (C0 control)');
        $this->invalidParseErrorTestHandler("\x1D", 0, 'U+001D (C0 control)');
        $this->invalidParseErrorTestHandler("\x1E", 0, 'U+001E (C0 control)');
        $this->invalidParseErrorTestHandler("\x1F", 0, 'U+001F (C0 control)');

        // DEL (U+007F)
        $this->invalidParseErrorTestHandler("\x7F", 0, 'U+007F');

        // C1 Controls
        $this->invalidParseErrorTestHandler("\xC2\x80", 1, 'U+0080 (C1 control)', "\xC2\x80");
        $this->invalidParseErrorTestHandler("\xC2\x9F", 1, 'U+009F (C1 control)', "\xC2\x9F");
        $this->invalidParseErrorTestHandler("\xC2\xA0", 0, 'U+00A0 (first codepoint above highest C1 control)', "\xC2\xA0");

        // Charcters surrounding surrogates
        $this->invalidParseErrorTestHandler("\xED\x9F\xBF", 0, 'U+D7FF (one codepoint below lowest surrogate codepoint)', "\xED\x9F\xBF");
        $this->invalidParseErrorTestHandler("\xEF\xBF\xBD", 0, 'U+DE00 (one codepoint above highest surrogate codepoint)');

        // Permanent noncharacters
        $this->invalidParseErrorTestHandler("\xEF\xB7\x90", 1, 'U+FDD0 (permanent noncharacter)', "\xEF\xB7\x90");
        $this->invalidParseErrorTestHandler("\xEF\xB7\xAF", 1, 'U+FDEF (permanent noncharacter)', "\xEF\xB7\xAF");
        $this->invalidParseErrorTestHandler("\xEF\xBF\xBE", 1, 'U+FFFE (permanent noncharacter)', "\xEF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xEF\xBF\xBF", 1, 'U+FFFF (permanent noncharacter)', "\xEF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF0\x9F\xBF\xBE", 1, 'U+1FFFE (permanent noncharacter)', "\xF0\x9F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF0\x9F\xBF\xBF", 1, 'U+1FFFF (permanent noncharacter)', "\xF0\x9F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF0\xAF\xBF\xBE", 1, 'U+2FFFE (permanent noncharacter)', "\xF0\xAF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF0\xAF\xBF\xBF", 1, 'U+2FFFF (permanent noncharacter)', "\xF0\xAF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF0\xBF\xBF\xBE", 1, 'U+3FFFE (permanent noncharacter)', "\xF0\xBF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF0\xBF\xBF\xBF", 1, 'U+3FFFF (permanent noncharacter)', "\xF0\xBF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF1\x8F\xBF\xBE", 1, 'U+4FFFE (permanent noncharacter)', "\xF1\x8F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF1\x8F\xBF\xBF", 1, 'U+4FFFF (permanent noncharacter)', "\xF1\x8F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF1\x9F\xBF\xBE", 1, 'U+5FFFE (permanent noncharacter)', "\xF1\x9F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF1\x9F\xBF\xBF", 1, 'U+5FFFF (permanent noncharacter)', "\xF1\x9F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF1\xAF\xBF\xBE", 1, 'U+6FFFE (permanent noncharacter)', "\xF1\xAF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF1\xAF\xBF\xBF", 1, 'U+6FFFF (permanent noncharacter)', "\xF1\xAF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF1\xBF\xBF\xBE", 1, 'U+7FFFE (permanent noncharacter)', "\xF1\xBF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF1\xBF\xBF\xBF", 1, 'U+7FFFF (permanent noncharacter)', "\xF1\xBF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF2\x8F\xBF\xBE", 1, 'U+8FFFE (permanent noncharacter)', "\xF2\x8F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF2\x8F\xBF\xBF", 1, 'U+8FFFF (permanent noncharacter)', "\xF2\x8F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF2\x9F\xBF\xBE", 1, 'U+9FFFE (permanent noncharacter)', "\xF2\x9F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF2\x9F\xBF\xBF", 1, 'U+9FFFF (permanent noncharacter)', "\xF2\x9F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF2\xAF\xBF\xBE", 1, 'U+AFFFE (permanent noncharacter)', "\xF2\xAF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF2\xAF\xBF\xBF", 1, 'U+AFFFF (permanent noncharacter)', "\xF2\xAF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF2\xBF\xBF\xBE", 1, 'U+BFFFE (permanent noncharacter)', "\xF2\xBF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF2\xBF\xBF\xBF", 1, 'U+BFFFF (permanent noncharacter)', "\xF2\xBF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF3\x8F\xBF\xBE", 1, 'U+CFFFE (permanent noncharacter)', "\xF3\x8F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF3\x8F\xBF\xBF", 1, 'U+CFFFF (permanent noncharacter)', "\xF3\x8F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF3\x9F\xBF\xBE", 1, 'U+DFFFE (permanent noncharacter)', "\xF3\x9F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF3\x9F\xBF\xBF", 1, 'U+DFFFF (permanent noncharacter)', "\xF3\x9F\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF3\xAF\xBF\xBE", 1, 'U+EFFFE (permanent noncharacter)', "\xF3\xAF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF3\xAF\xBF\xBF", 1, 'U+EFFFF (permanent noncharacter)', "\xF3\xAF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF3\xBF\xBF\xBE", 1, 'U+FFFFE (permanent noncharacter)', "\xF3\xBF\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF3\xBF\xBF\xBF", 1, 'U+FFFFF (permanent noncharacter)', "\xF3\xBF\xBF\xBF");
        $this->invalidParseErrorTestHandler("\xF4\x8F\xBF\xBE", 1, 'U+10FFFE (permanent noncharacter)', "\xF4\x8F\xBF\xBE");
        $this->invalidParseErrorTestHandler("\xF4\x8F\xBF\xBF", 1, 'U+10FFFF (permanent noncharacter)', "\xF4\x8F\xBF\xBF");

        $this->invalidParseErrorTestHandler("\xED\xA0\x80", 0, 'U+D800 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xAD\xBF", 0, 'U+DB7F (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xAE\x80", 0, 'U+DB80 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xAF\xBF", 0, 'U+DBFF (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xB0\x80", 0, 'U+DC00 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xBE\x80", 0, 'U+DF80 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xBF\xBF", 0, 'U+DFFF (UTF-16 surrogate character)'); // Paired UTF-16 surrogates
        $this->invalidParseErrorTestHandler("\xED\xA0\x80\xED\xB0\x80", 0, 'U+D800 U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xA0\x80\xED\xBF\xBF", 0, 'U+D800 U+DFFF (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAD\xBF\xED\xB0\x80", 0, 'U+DB7F U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAD\xBF\xED\xBF\xBF", 0, 'U+DB7F U+DFFF (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAE\x80\xED\xB0\x80", 0, 'U+DB80 U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAE\x80\xED\xBF\xBF", 0, 'U+DB80 U+DFFF (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAF\xBF\xED\xB0\x80", 0, 'U+DBFF U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAF\xBF\xED\xBF\xBF", 0, 'U+DBFF U+DFFF (paired UTF-16 surrogates)');
    }
}
