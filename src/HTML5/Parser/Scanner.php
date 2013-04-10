<?php
namespace HTML5\Parser;

/**
 * The scanner.
 *
 * This scans over an input stream.
 */
class Scanner {
  const CHARS_HEX = 'abcdefABCDEF01234567890';
  const CHARS_ALNUM = 'abcdefAghijklmnopqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
  const CHARS_ALPHA = 'abcdefAghijklmnopqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ';


  protected $char;
  protected $is;

  public function __construct($input) {
    $this->is = $input;
  }

  public function position() {
    return $this->is->position();
  }

  public function peek() {
    return $this->is->peek();
  }

  public function next() {
    $this->char = $this->is->char();
    return $this->char;
  }

  public function current() {
    return $this->char;
  }

  public function unconsume($howMany = 1) {
    for ($i = 0; $i < $howMany; ++$i) {
      $this->is->unconsume();
    }
  }

  public function getHex() {
    $this->charsWhile(self::CHARS_HEX);
  }
  public function getAsciiAlpha() {
    $this->charsWhile(self::CHARS_ALPHA);
  }
  public function getAsciiAlphaNum() {
    $this->charsWhile(self::CHARS_ALNUM);
  }
  public function getNumeric() {
    $this->charsWhile('0123456789');
  }


}
class ParseError extends Exception {
}
