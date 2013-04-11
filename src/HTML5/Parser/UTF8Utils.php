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

  /**
   * Checks for Unicode code points that are not valid in a document.
   *
   * @param string $data
   *   A string to analyze.
   * @return array
   *   An array of (string) error messages produced by the scanning.
   */
  public static function checkForIllegalCodepoints($data) {
    if (!function_exists('preg_match_all')) {
      throw \Exception('The PCRE library is not loaded or is not available.');
    }

    // Vestigal error handling.
    $errors = array();

    /* All U+0000 NULL characters in the input must be replaced
    by U+FFFD REPLACEMENT CHARACTERs. Any occurrences of such
    characters is a parse error. */
    for ($i = 0, $count = substr_count($data, "\0"); $i < $count; $i++) {
      $errors[] = 'null-character';
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
      $this[] =  'invalid-codepoint';
    }
    return $errors;
  }
}
