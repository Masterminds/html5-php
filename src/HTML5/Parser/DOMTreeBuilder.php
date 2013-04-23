<?php
namespace HTML5\Parser;

/**
 * Create an HTML5 DOM tree from events.
 *
 * This attempts to create a DOM from events emitted by a parser. This 
 * attempts (but does not guarantee) to up-convert older HTML documents 
 * to HTML5. It does this by applying HTML5's rules, but it will not 
 * change the architecture of the document itself.
 */
class DOMTreeBuilder implements EventHandler {
  protected $stack = array();
  protected $current; // Pointer in the tag hierarchy.
  protected $doc;

  protected $processor;

  /**
   * Quirks mode is enabled by default. Any document that is missing the 
   * DT will be considered to be in quirks mode.
   */
  protected $quirks = TRUE;

  public function __construct() {
    // XXX:
    // Create the doctype. For now, we are always creating HTML5 
    // documents, and attempting to up-convert any older DTDs to HTML5.
    $dt = \DOMImplementation::createDocumentType('html');
    $this->doc = \DOMImplementation::createDocument(NULL, 'html', $dt);
    $this->doc->errors = array();

    $this->current = $this->doc->documentElement();
  }

  /**
   * Provide an instruction processor.
   *
   * This is used for handling Processor Instructions as they are
   * inserted. If omitted, PI's are inserted directly into the DOM tree.
   */
  public function setInstructionProcessor(\HTML5\InstructionProcessor $proc) {
    $this->processor = $proc;
  }

  public function doctype($name, $idType = 0, $id = NULL, $quirks = FALSE) {
    // This is used solely for setting quirks mode. Currently we don't 
    // try to preserve the inbound DT. We convert it to HTML5.
    $this->quirks = $quirks;
  }

  public function startTag($name, $attributes = array(), $selfClosing = FALSE) {
    $lname = $this->normalizeTagName($name);


    // XXX: Since we create the root element, we skip this if it occurs
    // inside of the builder. We should probably check to make sure that
    // there is only one element so far, and indicate an error if there
    // is a structural problem.
    if ($lname == 'html') {
      return;
    }

    $ele = $this->doc->createElement($lname);

    $this->current->appendChild($ele);

    // XXX: Need to handle self-closing tags and unary tags.
    $this->current = $ele;
  }

  public function endTag($name) {
    $lname = $this->normalizeTagName($name);
    if ($this->current->tagName() != $lname) {
      return $this->quirksTreeResolver($lname);
    }

    // XXX: HTML has no parent. What do we do, though,
    // if this element appears in the wrong place?
    if ($lname == 'html') {
      return;
    }
    $this->current = $this->current->parentNode;
  }

  public function comment($cdata) {
    $node = $this->doc->createComment($cdata);
    $this->current->appendChild($node);
  }

  public function text($data) {
    $node = $this->doc->createTextNode($data);
    $this->current->appendChild($node);
  }

  public function eof() {
    // If the $current isn't the $root, do we need to do anything?
  }

  public function parseError($msg, $line, $col) {
    $this->doc->errors[] = sprintf("Line %d, Col %d: %s", $line, $col, $msg);
  }

  public function cdata($data) {
    $node = $this->doc->createCDATASection($data);
  }

  public function processingInstruction($name, $data = NULL) {
    // Important: The processor may modify the current DOM tree however 
    // it sees fit.
    if (isset($this->processor)) {
      $res = $processor->process($this->current, $name, $data);
      if (!empty($res)) {
        $this->current = $res;
      }
    }
  }

  // ==========================================================================
  // UTILITIES
  // ==========================================================================

  protected function normalizeTagName($name) {
    if (strpos($name, ':') !== FALSE) {
      // We know from the grammar that there must be at least one other 
      // char besides :, since : is not a legal tag start.
      $parts = explode(':', $name);
      return array_pop($parts);
    }

    return $name;
  }

  protected function quirksTreeResolver($name) {
    throw new \Exception("Not implemented.");

  }
}
