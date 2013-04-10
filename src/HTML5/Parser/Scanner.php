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
   * @param \HTML5\Parser\InputStream $input
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

  /**
   * Get the next group of that contains hex characters.
   *
   * Note, along with getting the characters the pointer in the data will be
   * moved as well.
   * 
   * @return string
   *   The next group that is hex characters.
   */
  public function getHex() {
    return $this->is->charsWhile(self::CHARS_HEX);
  }

  /**
   * Get the next group of characters that are ASCII Alpha characters.
   *
   * Note, along with getting the characters the pointer in the data will be
   * moved as well.
   * 
   * @return string
   *   The next group of ASCII alpha characters.
   */
  public function getAsciiAlpha() {
    return $this->is->charsWhile(self::CHARS_ALPHA);
  }

  /**
   * Get the next group of characters that are ASCII Alpha characters and numbers.
   *
   * Note, along with getting the characters the pointer in the data will be
   * moved as well.
   * 
   * @return string
   *   The next group of ASCII alpha characters and numbers.
   */
  public function getAsciiAlphaNum() {
    return $this->is->charsWhile(self::CHARS_ALNUM);
  }

  /**
   * Get the next group of numbers.
   *
   * Note, along with getting the characters the pointer in the data will be
   * moved as well.
   * 
   * @return string
   *   The next group of numbers.
   */
  public function getNumeric() {
    return $this->is->charsWhile('0123456789');
  }
}
