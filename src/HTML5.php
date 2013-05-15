<?php

use HTML5\Parser\StringInputStream;
use HTML5\Parser\FileInputStream;
use HTML5\Parser\Scanner;
use HTML5\Parser\Tokenizer;
use HTML5\Parser\DOMTreeBuilder;
use HTML5\Serializer\Serializer;

/**
 * The main HTML5 front end.
 *
 * This class offers convenience methods for parsing and serializing HTML5.
 * It is roughly designed to mirror the \DOMDocument class that is 
 * provided with most versions of PHP.
 *
 * EXPERIMENTAL. This may change or be completely replaced.
 */
class HTML5 {

  /**
   * Load and parse an HTML file.
   *
   * This will apply the HTML5 parser, which is tolerant of many 
   * varieties of HTML, including XHTML 1, HTML 4, and well-formed HTML 
   * 3. Note that in these cases, not all of the old data will be 
   * preserved. For example, XHTML's XML declaration will be removed.
   *
   * The rules governing parsing are set out in the HTML 5 spec.
   *
   * @param string $file
   *   The path to the file to parse. If this is a resource, it is 
   *   assumed to be an open stream whose pointer is set to the first 
   *   byte of input.
   * @param array $options
   *   An array of options.
   * @return \DOMDocument
   *   A DOM document. These object type is defined by the libxml 
   *   library, and should have been included with your version of PHP.
   */
  public static function load($file, $options = NULL) {

    // Handle the case where file is a resource.
    if (is_resource($file)) {
      // FIXME: We need a StreamInputStream class.
      return self::loadHTML(stream_get_contents($file));
    }

    $input = new FileInputStream($file);
    return self::parse($input);
  }

  /**
   * Parse an HTML string.
   * 
   * Take a string of HTML 5 (or earlier) and parse it into a 
   * DOMDocument.
   *
   *
   * @param array $options
   *   An array of options.
   * @return \DOMDocument
   *   A DOM document. DOM is part of libxml, which is included with 
   *   almost all distribtions of PHP.
   */
  public static function loadHTML($string, $options = NULL) {
    $input = new StringInputStream($string);
    return self::parse($input);
  }

  /**
   * Convenience function to load an HTML file.
   *
   * This is here to provide backwards compatibility with the
   * PHP DOM implementation. It simply calls load().
   */
  public static function loadHTMLFile($file, $options = NULL) {
    return self::load($file, $options);
  }

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

  /**
   * Parse an input stream.
   */
  public static function parse($input) {
    $events = new DOMTreeBuilder();
    $scanner = new Scanner($input);
    $parser = new Tokenizer($scanner, $events);

    $parser->parse();

    return $events->document();
  }

}
