<?php
namespace HTML5;

/**
 * Traverser for walking a DOM tree.
 *
 * This is a concrete traverser designed to convert a DOM tree into an 
 * HTML5 document. It is not intended to be a generic DOMTreeWalker 
 * implementation.
 */
class Traverser {

  protected $dom;
  protected $out;

  /**
   * Create a traverser.
   *
   * @param DOMNode $dom
   *   The document or node to traverse.
   * @param resource $out
   *   A stream that allows writing. The traverser will output into this 
   *   stream.
   */
  public function __construct($dom, $out) {
    $this->dom = $dom;
    $this->out = $out;
  }

  /**
   * Tell the traverser to walk the DOM.
   */
  public function walk() {
  }

}
