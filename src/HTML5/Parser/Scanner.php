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

  /**
   * Create a new Scanner.
   *
   * @param \HTML5\InputStream $input
   *   An InputStream to be scanned.
   */
  public function __construct($input) {
    $this->is = $input;
  }

  /**
   * Get the current position.
   *
   * @return int
   *   The current intiger byte position.
   */
  public function position() {
    return $this->is->position();
  }

  /**
   * Take a peek at the next character in the data.
   *
   * @return string
   *   The next character.
   */
  public function peek() {
    return $this->is->peek();
  }

  /**
   * Get the next character.
   * 
   * Note: This advances the pointer.
   *
   * @return string
   *   The next character.
   */
  public function next() {
    $this->char = $this->is->char();
    return $this->char;
  }

  /**
   * Get the current character.
   *
   * Note, this does not advance the pointer.
   * 
   * @return string
   *   The current character.
   */
  public function current() {
    return $this->char;
  }

  /**
   * Unconsume some of the data. This moves the data pointer backwards.
   *
   * @param  int $howMany
   *   The number of characters to move the pointer back.
   */
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
