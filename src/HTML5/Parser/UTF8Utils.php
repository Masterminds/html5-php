<?php
namespace Masterminds\HTML5\Parser;

/*
 *
* Portions based on code from html5lib files with the following copyright:

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
use voku\helper\UTF8;

/**
 * UTF-8 Utilities
 */
class UTF8Utils
{

    /**
     * The Unicode replacement character..
     */
    const FFFD = "\xEF\xBF\xBD";

    /**
     * Count the number of characters in a string.
     *
     * UTF-8 aware. This will try (in order) iconv,
     * MB, libxml, and finally a custom counter.
     *
     * @todo Move this to a general utility class.
     */
    public static function countChars($string)
    {
        return UTF8::strlen($string);
    }

    /**
     * Convert data from the given encoding to UTF-8.
     *
     * This has not yet been tested with charactersets other than UTF-8.
     * It should work with ISO-8859-1/-13 and standard Latin Win charsets.
     *
     * @param string $data
     *            The data to convert.
     * @param string $encoding
     *            A valid encoding. Examples: http://www.php.net/manual/en/mbstring.supported-encodings.php
     * @return string
     */
    public static function convertToUTF8($data, $encoding = 'UTF-8')
    {
        $forceEncoding = true;
        if ($encoding === 'auto') {
            $forceEncoding = false;
        }

        /*
         * From the HTML5 spec: Given an encoding, the bytes in the input stream must be converted to Unicode characters for the tokeniser, as described by the rules for that encoding, except that the leading U+FEFF BYTE ORDER MARK character, if any, must not be stripped by the encoding layer (it is stripped by the rule below). Bytes or sequences of bytes in the original byte stream that could not be converted to Unicode characters must be converted to U+FFFD REPLACEMENT CHARACTER code points.
         */
        $data = UTF8::encode($encoding, $data, $forceEncoding);

        /*
         * One leading U+FEFF BYTE ORDER MARK character must be ignored if any are present.
         */
        $data = UTF8::clean($data, true);

        return $data;
    }

    /**
     * Checks for Unicode code points that are not valid in a document.
     *
     * @param string $data
     *            A string to analyze.
     * @return array An array of (string) error messages produced by the scanning.
     */
    public static function checkForIllegalCodepoints($data)
    {
        if (!function_exists('preg_match_all')) {
            throw\Exception('The PCRE library is not loaded or is not available.');
        }

        // Vestigal error handling.
        $errors = array();

        /*
         * All U+0000 null characters in the input must be replaced by U+FFFD REPLACEMENT CHARACTERs. Any occurrences of such characters is a parse error.
         */
        for ($i = 0, $count = UTF8::substr_count($data, "\0"); $i < $count; $i++) {
            $errors[] = 'null-character';
        }

        /*
         * Any occurrences of any characters in the ranges U+0001 to U+0008, U+000B, U+000E to U+001F, U+007F to U+009F, U+D800 to U+DFFF , U+FDD0 to U+FDEF, and characters U+FFFE, U+FFFF, U+1FFFE, U+1FFFF, U+2FFFE, U+2FFFF, U+3FFFE, U+3FFFF, U+4FFFE, U+4FFFF, U+5FFFE, U+5FFFF, U+6FFFE, U+6FFFF, U+7FFFE, U+7FFFF, U+8FFFE, U+8FFFF, U+9FFFE, U+9FFFF, U+AFFFE, U+AFFFF, U+BFFFE, U+BFFFF, U+CFFFE, U+CFFFF, U+DFFFE, U+DFFFF, U+EFFFE, U+EFFFF, U+FFFFE, U+FFFFF, U+10FFFE, and U+10FFFF are parse errors. (These are all control characters or permanently undefined Unicode characters.)
         */
        // Check PCRE is loaded.
        $count = preg_match_all(
            '/(?:
        [\x01-\x08\x0B\x0E-\x1F\x7F] # U+0001 to U+0008, U+000B,  U+000E to U+001F and U+007F
      |
        \xC2[\x80-\x9F] # U+0080 to U+009F
      |
        \xED(?:\xA0[\x80-\xFF]|[\xA1-\xBE][\x00-\xFF]|\xBF[\x00-\xBF]) # U+D800 to U+DFFFF
      |
        \xEF\xB7[\x90-\xAF] # U+FDD0 to U+FDEF
      |
        \xEF\xBF[\xBE\xBF] # U+FFFE and U+FFFF
      |
        [\xF0-\xF4][\x8F-\xBF]\xBF[\xBE\xBF] # U+nFFFE and U+nFFFF (1 <= n <= 10_{16})
      )/x', $data, $matches);
        for ($i = 0; $i < $count; $i++) {
            $errors[] = 'invalid-codepoint';
        }

        return $errors;
    }
}
