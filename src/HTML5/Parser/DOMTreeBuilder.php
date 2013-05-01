<?php
namespace HTML5\Parser;

use HTML5\Elements;
/**
 * Create an HTML5 DOM tree from events.
 *
 * This attempts to create a DOM from events emitted by a parser. This 
 * attempts (but does not guarantee) to up-convert older HTML documents 
 * to HTML5. It does this by applying HTML5's rules, but it will not 
 * change the architecture of the document itself.
 */
class DOMTreeBuilder implements EventHandler {


  /**
   * Defined in 8.2.5.
   */
  const IM_INITIAL = 0;
  const IM_BEFORE_HTML = 1;
  const IM_BEFORE_HEAD = 2;
  const IM_IN_HEAD = 3;
  const IM_IN_HEAD_NOSCRIPT = 4;
  const IM_AFTER_HEAD = 5;
  const IM_IN_BODY = 6;
  const IM_TEXT = 7;
  const IM_IN_TABLE = 8;
  const IM_IN_TABLE_TEXT = 9;
  const IM_IN_CAPTION = 10;
  const IM_IN_COLUMN_GROUP = 11;
  const IM_IN_TABLE_BODY = 12;
  const IM_IN_ROW = 13;
  const IM_IN_CELL = 14;
  const IM_IN_SELECT = 15;
  const IM_IN_SELECT_IN_TABLE = 16;
  const IM_AFTER_BODY = 17;
  const IM_IN_FRAMESET = 18;
  const IM_AFTER_FRAMESET = 19;
  const IM_AFTER_AFTER_BODY = 20;
  const IM_AFTER_AFTER_FRAMESET = 21;

  protected $stack = array();
  protected $current; // Pointer in the tag hierarchy.
  protected $doc;

  protected $processor;

  protected $insertMode = 0;

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
    //$this->doc = \DOMImplementation::createDocument(NULL, 'html', $dt);
    $this->doc = \DOMImplementation::createDocument(NULL, NULL, $dt);
    $this->doc->errors = array();

    // $this->current = $this->doc->documentElement;
    $this->current = $this->doc; //->documentElement;
  }

  /**
   * Get the document.
   */
  public function document() {
    return $this->doc;
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

    if ($this->insertMode > self::IM_INITIAL) {
      $this->parseError("Illegal placement of DOCTYPE tag. Ignoring: " . $name);
      return;
    }

    $this->insertMode = self::IM_BEFORE_HTML;
  }

  public function startTag($name, $attributes = array(), $selfClosing = FALSE) {
    $lname = $this->normalizeTagName($name);

    // Make sure we have an html element.
    if (!$this->doc->documentElement && $name !== 'html') {
      $this->startTag('html');
    }

    // Set quirks mode if we're at IM_INITIAL with no doctype.
    if ($this->insertMode == self::IM_INITIAL) {
      $this->quirks = TRUE;
      $this->parseError("No DOCTYPE specified.");
    }

    // SPECIAL TAG HANDLING:
    // Spec says do this, and "don't ask."
    if ($name == 'image') {
      $name = 'img';
    }
    elseif ($name == 'optgroup' && $this->current->tagName == 'option') {
      $this->current = $this->current->parentNode;
    }
    // TODO: MathML support
    elseif ($name == 'math') {
    }
    // TODO: SVG support.
    elseif ($name == 'svg') {
    }


    // Autoclose p tags where appropriate.
    if ($this->insertMode >= self::IM_IN_BODY && Elements::isA($name, Elements::AUTOCLOSE_P)) {
      $this->autoclose('p');
    }

    // Set insert mode:
    switch ($name) {
    case 'html':
      $this->insertMode = self::IM_BEFORE_HEAD;
      break;
    case 'head':
      if ($this->insertMode > self::IM_BEFORE_HEAD) {
        $this->parseError("Unexpected head tag outside of head context.");
      }
      else {
        $this->isertMode = self::IM_IN_HEAD;
      }
      break;
    case 'body':
      $this->insertMode = self::IM_IN_BODY;
      break;
    case 'noscript':
      if ($this->insertMode == self::IM_IN_HEAD) {
        $this->insertMode = self::IM_IN_HEAD_NOSCRIPT;
      }
      break;

    }


    $ele = $this->doc->createElement($lname);
    foreach ($attributes as $aName => $aVal) {
      $ele->setAttribute($aName, $aVal);

      // This is necessary on a non-DTD schema, like HTML5.
      if ($aName == 'id') {
        $ele->setIdAttribute('id', TRUE);
      }
    }

    $this->current->appendChild($ele);

    // XXX: Need to handle self-closing tags and unary tags.
    if (!Elements::isA($name, Elements::UNARY_TAG)) {
      $this->current = $ele;
    }

    // Return the element mask, which the tokenizer can then use to set 
    // various processing rules.
    return Elements::element($name);
  }

  public function endTag($name) {
    $lname = $this->normalizeTagName($name);

    // Ignore closing tags for unary elements.
    if (Elements::isA($name, Elements::UNARY_TAG)) {
      return;
    }

    if ($this->insertMode <= self::IM_BEFORE_HTML) {
      // 8.2.5.4.2
      if (in_array($name, array('html', 'br', 'head', 'title'))) {
        $this->startTag('html');
        $this->endTag($name);
        $this->insertMode = self::IM_BEFORE_HEAD;
        return;
      }

      // Ignore the tag.
      $this->parseError("Illegal closing tag at global scope.");
      return;
    }

    if ($name != $lname) {
      fprintf(STDOUT, "Mismatch on %s and %s", $name, $lname);
      return $this->quirksTreeResolver($lname);
    }

    // XXX: HTML has no parent. What do we do, though,
    // if this element appears in the wrong place?
    if ($lname == 'html') {
      return;
    }

    //$this->current = $this->current->parentNode;
    if (!$this->autoclose($name)) {
      $this->parseError('Could not find closing tag for ' . $name);
    }

    switch ($this->insertMode) {
    case "head":
      $this->insertMode = self::IM_AFTER_HEAD;
      break;
    case "body":
      $this->insertMode = self::IM_AFTER_BODY;
      break;
    }

    // 8.2.5.4.7
    if ($name == 'sarcasm') {
      $this->text("Take a deep breath.");
    }
  }

  public function comment($cdata) {
    // TODO: Need to handle case where comment appears outside of the HTML tag.
    $node = $this->doc->createComment($cdata);
    $this->current->appendChild($node);
  }

  public function text($data) {
    if ($this->insertMode < self::IM_IN_HEAD) {
      $data = trim($data);
      if (!empty($data)) {
        $this->parseError("Unexpected text. Ignoring: " . $data);
        return;
      }
    }
    $node = $this->doc->createTextNode($data);
    $this->current->appendChild($node);
  }

  public function eof() {
    // If the $current isn't the $root, do we need to do anything?
  }

  public function parseError($msg, $line = 0, $col = 0) {
    $this->doc->errors[] = sprintf("Line %d, Col %d: %s", $line, $col, $msg);
  }

  public function cdata($data) {
    $node = $this->doc->createCDATASection($data);
    $this->current->appendChild($node);
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

  /**
   * Automatically climb the tree and close the closest node with the matching $tag.
   */
  protected function autoclose($tag) {
    $working = $this->current;
    do {
      if ($working->nodeType != XML_ELEMENT_NODE) {
        return FALSE;
      }
      if ($working->tagName == $tag) {
        $this->current = $working->parentNode;
        return TRUE;
      }
    } while ($working = $working->parentNode);
    return FALSE;

  }


}
