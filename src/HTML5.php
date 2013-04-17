<?php

/**
 * The main HTML5 front end.
 *
 * This class offers convenience methods for parsing and serializing HTML5.
 *
 * EXPERIMENTAL. This may change or be completely replaced.
 */
class HTML5 extends \HTML5\Parser {
  // Inherit parse() and parseFragment().

  /**
   * Save a DOM into a given file as HTML5.
   */
  public static function save($dom, $file) {
    $serializer = new \HTML5\Serializer\Serializer($dom);
    return $serializer->save($file);
  }

  /**
   * Convert a DOM into an HTML5 string.
   */
  public static function saveHTML($dom) {
    $serializer = new \HTML5\Serializer\Serializer($dom);
    return $serializer->saveHTML();
  }
}
