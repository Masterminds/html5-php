<?php
namespace HTML5\Parser;

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

class StringInputStream implements InputStream {
  /**
   * The string data we're parsing.
   */
  private $data;

  /**
   * The current integer byte position we are in $data
   */
  private $char;

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
   * @param $data Data to parse
   */
  public function __construct($data, $encoding = 'UTF-8') {

    $data = $this->convertToUTF8($data, $encoding);

    // There is good reason to question whether it makes sense to
    // do this here, since most of these checks are done during
    // parsing, and since this check doesn't actually *do* anything.
    $e = UTF8Utils::checkForIllegalCodepoints($data);
    if (!empty($e)) {
      throw new ParseError("UTF-8 encoding issues: " . implode(', ', $e));
    }

    $data = $this->replaceLinefeeds($data);

    $this->data = $data;
    $this->char = 0;
    $this->EOF  = strlen($data);
  }

  /**
   * Convert data from the given encoding to UTF-8.
   *
   * This has not yet been tested with charactersets other than UTF-8. 
   * It should work with ISO-8859-1/-13 and standard Latin Win charsets.
   *
   * @param string $data
   *   The data to convert.
   * @param string $encoding
   *   A valid encoding. Examples: http://www.php.net/manual/en/mbstring.supported-encodings.php
   */
  protected function convertToUTF8($data, $encoding = 'UTF-8') {
    /* Given an encoding, the bytes in the input stream must be
    converted to Unicode characters for the tokeniser, as
    described by the rules for that encoding, except that the
    leading U+FEFF BYTE ORDER MARK character, if any, must not
    be stripped by the encoding layer (it is stripped by the rule below).

    Bytes or sequences of bytes in the original byte stream that
    could not be converted to Unicode characters must be converted
    to U+FFFD REPLACEMENT CHARACTER code points. */

    // XXX currently assuming input data is UTF-8; once we
    // build encoding detection this will no longer be the case
    //
    // We previously had an mbstring implementation here, but that
    // implementation is heavily non-conforming, so it's been
    // omitted.
    if (function_exists('iconv') && $encoding != 'auto') {
      // iconv has the following behaviors:
      // - Overlong representations are ignored.
      // - Beyond Plane 16 is replaced with a lower char.
      // - Incomplete sequences generate a warning.
      $data = @iconv($encoding, 'UTF-8//IGNORE', $data);
    }
    // MPB: Testing the newer mb_convert_encoding(). This might need
    // to be removed again.
    elseif (function_exists('mb_convert_encoding')) {
      // mb library has the following behaviors:
      // - UTF-16 surrogates result in FALSE.
      // - Overlongs and outside Plane 16 result in empty strings.
      $data = mb_convert_encoding($data, 'UTF-8', $encoding);
    }
    else {
      // we can make a conforming native implementation
      throw new Exception('Not implemented, please install mbstring or iconv');
    }

    /* One leading U+FEFF BYTE ORDER MARK character must be
    ignored if any are present. */
    if (substr($data, 0, 3) === "\xEF\xBB\xBF") {
      $data = substr($data, 3);
    }

    return $data;
  }

  /**
   * Replace linefeed characters according to the spec.
   */
  protected function replaceLinefeeds($data) {
    /* U+000D CARRIAGE RETURN (CR) characters and U+000A LINE FEED
    (LF) characters are treated specially. Any CR characters
    that are followed by LF characters must be removed, and any
    CR characters not followed by LF characters must be converted
    to LF characters. Thus, newlines in HTML DOMs are represented
    by LF characters, and there are never any CR characters in the
    input to the tokenization stage. */
    $crlfTable = array(
        "\0" =>  "\xEF\xBF\xBD",
        "\r\n" => "\n",
        "\r" => "\n",
    );
    return strtr($data, $crlfTable);
  }

  /**
   * Checks for Unicode code points that are not valid in a document.
   *
   * This stores a parse error for each error that is found.
   */
  protected function checkForIllegalCodepoints($data) {
    if (!function_exists('preg_match_all')) {
      throw \Exception('The PCRE library is not loaded or is not available.');
    }

    /* All U+0000 NULL characters in the input must be replaced
    by U+FFFD REPLACEMENT CHARACTERs. Any occurrences of such
    characters is a parse error. */
    for ($i = 0, $count = substr_count($data, "\0"); $i < $count; $i++) {
      $this->errors[] = array(
        'type' => Tokenizer::PARSEERROR,
        'data' => 'null-character'
      );
    }

    /* Any occurrences of any characters in the ranges U+0001 to
    U+0008, U+000B,  U+000E to U+001F,  U+007F  to U+009F,
    U+D800 to U+DFFF , U+FDD0 to U+FDEF, and
    characters U+FFFE, U+FFFF, U+1FFFE, U+1FFFF, U+2FFFE, U+2FFFF,
    U+3FFFE, U+3FFFF, U+4FFFE, U+4FFFF, U+5FFFE, U+5FFFF, U+6FFFE,
    U+6FFFF, U+7FFFE, U+7FFFF, U+8FFFE, U+8FFFF, U+9FFFE, U+9FFFF,
    U+AFFFE, U+AFFFF, U+BFFFE, U+BFFFF, U+CFFFE, U+CFFFF, U+DFFFE,
    U+DFFFF, U+EFFFE, U+EFFFF, U+FFFFE, U+FFFFF, U+10FFFE, and
    U+10FFFF are parse errors. (These are all control characters
    or permanently undefined Unicode characters.) */
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
      )/x',
      $data,
      $matches
    );
    for ($i = 0; $i < $count; $i++) {
      $this->errors[] = array(
        'type' => Tokenizer::PARSEERROR,
        'data' => 'invalid-codepoint'
      );
    }
  }

  /**
   * Returns the current line that the tokenizer is at.
   */
  public function currentLine() {
    if (empty($this->EOF) || $this->char == 0) {
      return 1;
    }
    // Add one to $this->char because we want the number for the next
    // byte to be processed.
    return substr_count($this->data, "\n", 0, min($this->char, $this->EOF)) + 1;
  }

  /**
   * @deprecated
   */
  public function getCurrentLine() {
    return currentLine();
  }

  /**
   * Returns the current column of the current line that the tokenizer is at.
   *
   * Newlines are column 0. The first char after a newline is column 1.
   *
   * @return int
   *   The column number.
   */
  public function columnOffset() {

    // Short circuit for the first char.
    if ($this->char == 0) {
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
    if ($lastLine !== FALSE) {
      $findLengthOf = substr($this->data, $lastLine + 1, $this->char - 1 - $lastLine);
    }
    else {
      // After a newline.
      $findLengthOf = substr($this->data, 0, $this->char);
    }

    return UTF8Utils::countChars($findLengthOf);
  }

  /**
   * @deprecated
   */
  public function getColumnOffset() {
    return $this->columnOffset();
  }

  /**
   * Get the current character.
   * 
   * @return string
   *   The current character.
   */
  public function current() {
    return $this->data[$this->char];
  }

  /**
   * Advance the pointer. This is part of the Iterator interface.
   */
  public function next() {
    $this->char++;
  }

  /**
   * Rewind to the start of the string.
   */
  public function rewind() {
    $this->char = 0;
  }

  /**
   * Is the current pointer location valid.
   *
   * @return bool
   *   Is the current pointer location valid.
   */
  public function valid() {
    if ($this->char < $this->EOF) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get all characters until EOF.
   *
   * This reads to the end of the file, and sets the read marker at the 
   * end of the file.
   *
   * @note This performs bounds checking
   */
  public function remainingChars() {
    if ($this->char < $this->EOF) {
      $data = substr($this->data, $this->char);
      $this->char = $this->EOF;
      return $data;
    }
    return FALSE;
  }

  /**
   * Read to a particular match (or until $max bytes are consumed).
   *
   * This operates on byte sequences, not characters.
   *
   * Matches as far as possible until we reach a certain set of bytes
   * and returns the matched substring.
   *
   * @param string $bytes
   *   Bytes to match.
   * @param int $max
   *   Maximum number of bytes to scan.
   * @return mixed
   *   Index or FALSE if no match is found. You should use strong 
   *   equality when checking the result, since index could be 0.
   */
  public function charsUntil($bytes, $max = null) {
    if ($this->char >= $this->EOF) {
      return FALSE;
    }

    if ($max === 0 || $max) {
      $len = strcspn($this->data, $bytes, $this->char, $max);
    }
    else {
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
   *   A mask of bytes to match. If ANY byte in this mask matches the 
   *   current char, the pointer advances and the char is part of the 
   *   substring.
   * @param int $max
   *   The max number of chars to read.
   */
  public function charsWhile($bytes, $max = null) {
    if ($this->char >= $this->EOF) {
      return FALSE;
    }

    if ($max === 0 || $max) {
      $len = strspn($this->data, $bytes, $this->char, $max);
    }
    else {
      $len = strspn($this->data, $bytes, $this->char);
    }
    $string = (string) substr($this->data, $this->char, $len);
    $this->char += $len;
    return $string;
  }

  /**
   * Unconsume characters.
   *
   * @param int $howMany
   *   The number of characters to unconsume.
   */
  public function unconsume($howMany = 1) {
    if (($this->char - $howMany) >= 0) {
      $this->char = $this->char - $howMany;
    }
  }

  public function peek() {
    if (($this->char + 1) <= $this->EOF) {
      return $this->data[$this->char + 1];
    }

    return FALSE;
  }

  public function key() {
    return $this->char;
  }
}
