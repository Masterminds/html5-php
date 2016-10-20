<?php
/**
 * Loads a string to be parsed.
 */
namespace Masterminds\HTML5\Parser;

/*
 *
* Based on code from html5lib:

Copyright 2009 Geoffrey Sneddon <http://gsnedders.com/>

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

// Some conventions:
// - /* */ indicates verbatim text from the HTML 5 specification
//   MPB: Not sure which version of the spec. Moving from HTML5lib to
//   HTML5-PHP, I have been using this version:
//   http://www.w3.org/TR/2012/CR-html5-20121217/Overview.html#contents
//
// - // indicates regular comments

use voku\helper\UTF8;

class StringInputStream implements InputStream
{

    /**
     * The string data we're parsing.
     */
    private $data;

    /**
     * The current integer byte position we are in $data
     *
     * @var int
     */
    private $char = 0;

    /**
     * Length of $data; when $char === $data, we are at the end-of-file.
     */
    private $EOF;

    /**
     * Parse errors.
     */
    public $errors = array();

    /**
     * Create a new InputStream wrapper.
     *
     * @param string $data <p>Data to parse.</p>
     * @param string $encoding
     * @param string $debug
     */
    public function __construct($data, $encoding = 'UTF-8', $debug = '')
    {
        $data = UTF8Utils::convertToUTF8($data, $encoding);

        if ($debug) {
            fprintf(STDOUT, $debug, $data, strlen($data));
        }

        // There is good reason to question whether it makes sense to
        // do this here, since most of these checks are done during
        // parsing, and since this check doesn't actually *do* anything.
        $this->errors = UTF8Utils::checkForIllegalCodepoints($data);
        // if (!empty($e)) {
        // throw new ParseError("UTF-8 encoding issues: " . implode(', ', $e));
        // }

        $data = $this->replaceLinefeeds($data);

        $this->data = $data;
        $this->char = 0;
        $this->EOF = strlen($data);
    }

    /**
     * Replace linefeed characters according to the spec.
     *
     * @param $data
     *
     * @return string
     */
    protected function replaceLinefeeds($data)
    {
        /*
         * U+000D CARRIAGE RETURN (CR) characters and U+000A LINE FEED (LF) characters are treated specially. Any CR characters that are followed by LF characters must be removed, and any CR characters not followed by LF characters must be converted to LF characters. Thus, newlines in HTML DOMs are represented by LF characters, and there are never any CR characters in the input to the tokenization stage.
         */
        $crlfTable = array(
            "\0" => "\xEF\xBF\xBD",
            "\r\n" => "\n",
            "\r" => "\n"
        );

        return UTF8::strtr($data, $crlfTable);
    }

    /**
     * Returns the current line that the tokenizer is at.
     */
    public function currentLine()
    {
        if (empty($this->EOF) || $this->char === 0) {
            return 1;
        }

        // Add one to $this->char because we want the number for the next
        // byte to be processed.
        return substr_count($this->data, "\n", 0, min($this->char, $this->EOF)) + 1;
    }

    /**
     * @deprecated
     */
    public function getCurrentLine()
    {
        return $this->currentLine();
    }

    /**
     * Returns the current column of the current line that the tokenizer is at.
     *
     * Newlines are column 0. The first char after a newline is column 1.
     *
     * @return int The column number.
     */
    public function columnOffset()
    {
        // Short circuit for the first char.
        if ($this->char === 0) {
            return 0;
        }

        // strrpos is weird, and the offset needs to be negative for what we
        // want (i.e., the last \n before $this->char). This needs to not have
        // one (to make it point to the next character, the one we want the
        // position of) added to it because strrpos's behaviour includes the
        // final offset byte.
        $backwardFrom = $this->char - 1 - strlen($this->data);
        $lastLine = strrpos($this->data, "\n", $backwardFrom);

        // However, for here we want the length up until the next byte to be
        // processed, so add one to the current byte ($this->char).
        if ($lastLine !== false) {
            $findLengthOf = substr($this->data, $lastLine + 1, $this->char - 1 - $lastLine);
        } else {
            // After a newline.
            $findLengthOf = substr($this->data, 0, $this->char);
        }

        return UTF8::strlen($findLengthOf);
    }

    /**
     *
     * @deprecated
     *
     */
    public function getColumnOffset()
    {
        return $this->columnOffset();
    }

    /**
     * Get the current character.
     *
     * @return string The current character.
     */
    public function current()
    {
        return $this->data[$this->char];
    }

    /**
     * Advance the pointer.
     * This is part of the Iterator interface.
     */
    public function next()
    {
        $this->char ++;
    }

    /**
     * Rewind to the start of the string.
     */
    public function rewind()
    {
        $this->char = 0;
    }

    /**
     * Is the current pointer location valid.
     *
     * @return bool Is the current pointer location valid.
     */
    public function valid()
    {
        if ($this->char < $this->EOF) {
            return true;
        }

        return false;
    }

    /**
     * Get all characters until EOF.
     *
     * This reads to the end of the file, and sets the read marker at the
     * end of the file.
     *
     * @note This performs bounds checking
     *
     * @return string <p>Returns the remaining text. If called when the InputStream is
     *         already exhausted, it returns an empty string.</p>
     */
    public function remainingChars()
    {
        if ($this->char < $this->EOF) {
            $data = substr($this->data, $this->char);
            $this->char = $this->EOF;

            return $data;
        }

        return ''; // false;
    }

    /**
     * Read to a particular match (or until $max bytes are consumed).
     *
     * This operates on byte sequences, not characters.
     *
     * Matches as far as possible until we reach a certain set of bytes
     * and returns the matched substring.
     *
     * @param string $bytes <p>Bytes to match.</p>
     * @param int $max <p>Maximum number of bytes to scan.</p>
     * @return mixed <p>Index or false if no match is found. You should use strong
     *         equality when checking the result, since index could be 0.</p>
     */
    public function charsUntil($bytes, $max = null)
    {
        if ($this->char >= $this->EOF) {
            return false;
        }

        if ($max === 0 || $max) {
            $len = strcspn($this->data, $bytes, $this->char, $max);
        } else {
            $len = strcspn($this->data, $bytes, $this->char);
        }

        $string = (string) substr($this->data, $this->char, $len);
        $this->char += $len;

        return $string;
    }

    /**
     * Returns the string so long as $bytes matches.
     *
     * Matches as far as possible with a certain set of bytes
     * and returns the matched substring.
     *
     * @param string $bytes
     *            <p>
     *            A mask of bytes to match. If ANY byte in this mask matches the
     *            current char, the pointer advances and the char is part of the
     *            substring.
     *            </p>
     * @param int $max <p>The max number of chars to read.</p>
     *
     * @return bool|string
     */
    public function charsWhile($bytes, $max = null)
    {
        if ($this->char >= $this->EOF) {
            return false;
        }

        if ($max === 0 || $max) {
            $len = strspn($this->data, $bytes, $this->char, $max);
        } else {
            $len = strspn($this->data, $bytes, $this->char);
        }
        $string = (string) substr($this->data, $this->char, $len);

        $this->char += $len;

        return $string;
    }

    /**
     * Unconsume characters.
     *
     * @param int $howMany <p>The number of characters to unconsume.</p>
     */
    public function unconsume($howMany = 1)
    {
        if (($this->char - $howMany) >= 0) {
            $this->char -= $howMany;
        }
    }

    /**
     * Look ahead without moving cursor.
     *
     * @return bool
     */
    public function peek()
    {
        if (($this->char + 1) <= $this->EOF) {
            return $this->data[$this->char + 1];
        }

        return false;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->char;
    }
}
