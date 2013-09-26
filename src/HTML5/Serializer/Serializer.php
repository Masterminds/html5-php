<?php
/**
 * A simple serializer that walks the DOM tree and outputs HTML5.
 */
namespace HTML5\Serializer;

use \HTML5\Serializer\OutputRules;

/**
 * Transform a DOM into an HTML5 document.
 *
 * This provides a serializer that roughly follows the save and load API 
 * in the native PHP DOM implementation.
 *
 * For reference, see DOMDocument::save, DOMDocument::saveXML, 
 * DOMDocument::saveHTML and DOMDocument::saveHTMLFile.
 */
class Serializer {
  protected $dom;
  protected $options = array();

  /**
   * Create a serializer.
   *
   * This takes a DOM-like data structure. It SHOULD treat the
   * DOMNode as an interface, but this does not do type checking.
   *
   * @param DOMNode $dom
   *   A DOMNode-like object. Typically, a DOMDocument should be passed.
   * @param array $options
   *   Options that can be passed into the serializer. These include:
   *   - format: a bool value to specify if formatting (e.g. add indentation)
   *     should be used on the output. Defaults to TRUE.
   *   - encode: Text written to the output is escaped by default and not all
   *     entities are encoded. If this is set to TRUE all entities will be encoded.
   *     Defaults to FALSE.
   */
  public function __construct($dom, $options = array()) {
    $this->dom = $dom;
    $this->options = $options;
  }

  /**
   * Save to a file.
   *
   * @param mixed $filename
   *   A file handle resource or the 
   *   full name to the file. This will overwrite the contents of 
   *   any file that it finds.
   */
  public function save($filename) {
    $close = TRUE;
    if (is_resource($filename)) {
      $file = $filename;
      $close = FALSE;
     }
    else {
      $file = fopen($filename, 'w');
    }
    $rules = new OutputRules($file, $this->options);
    $trav = new Traverser($this->dom, $file, $rules, $this->options);

    $trav->walk();

    if ($close) {
      fclose($file);
    }
  }

  /**
   * Return the DOM as an HTML5 string.
   */
  public function saveHTML() {
    // We buffer into a temp-file backed memory map. This may or may not be
    // faster than writing directly to a string, but it makes the interface
    // consistant and will keep memory consumption lower (2MB max for the file
    // buffer).
    $stream = fopen('php://temp', 'w');
    $this->save($stream);
    return stream_get_contents($stream, -1, 0);
  }
}
