<?php
namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Parser\FileInputStream;

class FileInputStreamTest extends \Masterminds\HTML5\Tests\TestCase
{

    public function testConstruct()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertInstanceOf('\Masterminds\HTML5\Parser\FileInputStream', $s);
    }

    public function testNext()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        $s->next();
        self::assertEquals('!', $s->current());
        $s->next();
        self::assertEquals('d', $s->current());
    }

    public function testKey()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertEquals(0, $s->key());

        $s->next();
        self::assertEquals(1, $s->key());
    }

    public function testPeek()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertEquals('!', $s->peek());

        $s->next();
        self::assertEquals('d', $s->peek());
    }

    public function testCurrent()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertEquals('<', $s->current());

        $s->next();
        self::assertEquals('!', $s->current());

        $s->next();
        self::assertEquals('d', $s->current());
    }

    public function testColumnOffset()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');
        self::assertEquals(0, $s->columnOffset());
        $s->next();
        self::assertEquals(1, $s->columnOffset());
        $s->next();
        self::assertEquals(2, $s->columnOffset());
        $s->next();
        self::assertEquals(3, $s->columnOffset());

        // Make sure we get to the second line
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        self::assertEquals(0, $s->columnOffset());

        $s->next();
        $canary = $s->current(); // h
        self::assertEquals('h', $canary);
        self::assertEquals(1, $s->columnOffset());
    }

    public function testCurrentLine()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertEquals(1, $s->currentLine());

        // Make sure we get to the second line
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        self::assertEquals(2, $s->currentLine());

        // Make sure we get to the third line
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        $s->next();
        self::assertEquals(3, $s->currentLine());
    }

    public function testRemainingChars()
    {
        $text = file_get_contents(__DIR__ . '/FileInputStreamTest.html');
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');
        self::assertEquals(
            str_replace(array("\n", "\r", "\r\n"), "", $text),
            str_replace(array("\n", "\r", "\r\n"), "", $s->remainingChars())
        );

        $text = substr(file_get_contents(__DIR__ . '/FileInputStreamTest.html'), 1);
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');
        $s->next(); // Pop one.
        self::assertEquals(
            str_replace(array("\n", "\r", "\r\n"), "", $text),
            str_replace(array("\n", "\r", "\r\n"), "", $s->remainingChars())
        );
    }

    public function testCharsUnitl()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertEquals('', $s->charsUntil('<'));
        // Pointer at '<', moves to ' '
        self::assertEquals('<!doctype', $s->charsUntil(' ', 20));

        // Pointer at ' ', moves to '>'
        self::assertEquals(' html', $s->charsUntil('>'));

        // Pointer at '>', moves to '\n'.
        self::assertEquals('>', $s->charsUntil("\n"));

        // Pointer at '\n', move forward then to the next'\n'.
        $s->next();
        self::assertEquals('<html lang="en">', $s->charsUntil("\n"));

        // Ony get one of the spaces.
        self::assertEquals("\n ", $s->charsUntil('<', 2));

        // Get the other space.
        self::assertEquals(" ", $s->charsUntil('<'));

        // This should scan to the end of the file.
        $text = "<head>
    <meta charset=\"utf-8\">
    <title>Test</title>
  </head>
  <body>
    <p>This is a test.</p>
  </body>
</html>";
        self::assertEquals(
            str_replace(array("\n", "\r", "\r\n"), "", $text),
            str_replace(array("\n", "\r", "\r\n"), "", $s->charsUntil("\t"))
        );
    }

    public function testCharsWhile()
    {
        $s = new FileInputStream(__DIR__ . '/FileInputStreamTest.html');

        self::assertEquals('<!', $s->charsWhile('!<'));
        self::assertEquals('', $s->charsWhile('>'));
        self::assertEquals('doctype', $s->charsWhile('odcyept'));
        self::assertEquals(' htm', $s->charsWhile('html ', 4));
    }
}
