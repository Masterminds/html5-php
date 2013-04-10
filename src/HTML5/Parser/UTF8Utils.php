<?php
namespace HTML5\Parser;
/**
 * UTF-8 Utilities
 */
class UTF8Utils {
  /**
   * Count the number of characters in a string.
   *
   * UTF-8 aware. This will try (in order) iconv,
   * MB, libxml, and finally a custom counter.
   *
   * @todo Move this to a general utility class.
   */
  public static function countChars($string) {
    // Get the length for the string we need.
    if(function_exists('iconv_strlen')) {
      return iconv_strlen($string, 'utf-8');
    }
    elseif(function_exists('mb_strlen')) {
      return mb_strlen($string, 'utf-8');
    }
    elseif(function_exists('utf8_decode')) {
      // MPB: Will this work? Won't certain decodes lead to two chars 
      // extrapolated out of 2-byte chars?
      return strlen(utf8_decode($string));
    }
    $count = count_chars($string);
    // 0x80 = 0x7F - 0 + 1 (one added to get inclusive range)
    // 0x33 = 0xF4 - 0x2C + 1 (one added to get inclusive range)
    return array_sum(array_slice($count, 0, 0x80)) +
         array_sum(array_slice($count, 0xC2, 0x33));
  }
}
