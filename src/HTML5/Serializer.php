<?php
/**
 * A simple serializer that walks the DOM tree and outputs HTML5.
 */
namespace HTML5;

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

  /**
   * Create a serializer.
   *
   * This takes a DOM-like data structure. It SHOULD treat the
   * DOMNode as an interface, but this does not do type checking.
   *
   * @param DOMNode $dom
   *   A DOMNode-like object. Typically, a DOMDocument should be passed.
   */
  public function __construct($dom) {
    $this->dom = $dom;
  }

  /**
   * Save to a file.
   *
   * @param string $filename
   *   The full name to the file. This will overwrite the contents of 
   *   any file that it finds.
   */
  public function save($filename) {
  }

  /**
   * Return the DOM as an HTML5 string.
   */
  public function saveHTML() {
  }
}
