<?php
namespace HTML5\Parser;

/**
 * Manage entity references.
 */
class CharacterReference {
  /**
   * Given a name (e.g. 'amp'), lookup the UTF-8 character ('&')
   *
   * @param string $name
   *   The name to look up.
   * @return string
   *   The character sequence. In UTF-8 this may be more than one byte.
   */
  public static function lookupName($name) {
    return '';
  }
  /**
   * Given a Unicode codepoint, return the UTF-8 character.
   */
  public static function lookupCode($codePoint) {
    return '';
  }

  /**
   * Given a decimal number, return the UTF-8 character.
   */
  public static function lookupDecimal($int) {
  }

  /**
   * Given a hexidecimal number, return the UTF-8 character.
   */
  public static function lookupHex($hexdec) {
  }
}
