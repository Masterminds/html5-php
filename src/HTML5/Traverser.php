<?php
namespace HTML5;

/**
 * Traverser for walking a DOM tree.
 *
 * This is a concrete traverser designed to convert a DOM tree into an 
 * HTML5 document. It is not intended to be a generic DOMTreeWalker 
 * implementation.
 *
 * @see http://www.w3.org/TR/2012/CR-html5-20121217/syntax.html#serializing-html-fragments
 */
class Traverser {

  // TODO: Refactor this into an element mask.
  static $block_elements = array(
    'html' => 1,
    'body' => 1,
    'head' => 1,
    'p' => 1,
    'div' => 1,
    'h1' => 1,
    'h2' => 1,
    'h3' => 1,
    'h4' => 1,
    'h5' => 1,
    'h6' => 1,
    'title' => 1,
    'script' => 1,
    'link' => 1,
    'meta' => 1,
    'section' => 1,
    'article' => 1,
    'table' => 1,
    'tbody' => 1,
    'tr' => 1,
    'th' => 1,
    'td' => 1,
    //'form' => 1,
  );

  // TODO: Refactor this into an element mask.
  static $literal_elements = array(
    'style' => 1,
    'script' => 1,
    'xmp' => 1,
    'iframe' => 1,
    'noembed' => 1,
    'noframes' => 1,
    'plaintext' => 1,
  );

  protected $dom;
  protected $out;
  protected $pretty = TRUE;

  const DOCTYPE = '<!DOCTYPE html>';

  /**
   * Create a traverser.
   *
   * @param DOMNode|DOMNodeList $dom
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
   *
   * @return resource $out
   *   Returns the output stream.
   */
  public function walk() {
    // If DOMDocument, start with the DOCTYPE and travers.
    if ($this->dom instanceof \DOMDocument) {
      $this->doctype();
      $this->document($this->dom);
    }
    // If NodeList, loop
    elseif ($this->dom instanceof \DOMNodeList) {
      // Loop through the list
    }
    // Else assume this is a DOMNode-like datastructure.
    else {
      $this->node($this->dom);
    }

    return $this->out;
  }

  protected function doctype() {
    $this->wr(self::DOCTYPE);
    $this->nl();
  }

  protected function document($node) {
    $this->node($node->documentElement);
    $this->nl();
  }

  protected function node($node) {
    switch ($node->nodeType) {
      case XML_ELEMENT_NODE:
        $this->element($node);
        break;
      case XML_TEXT_NODE:
        $this->text($node);
        break;
      case XML_CDATA_SECTION_NODE:
        $this->cdata($node);
        break;
      // FIXME: It appears that the parser doesn't do PI's.
      case XML_PI_NODE:
        $this->processorInstruction($ele);
        break;
      case XML_COMMENT_NODE:
        $this->comment($node);
        break;
      // Currently we don't support embedding DTDs.
      default:
        print '<!-- Skipped -->';
        break;
    }
  }

  protected function element($ele) {
    $name = $ele->tagName;
    $block = $this->pretty && $this->isBlock($name);

    // TODO: Really need to fix the spacing.
    // Add a newline for a block element.
    if ($block) $this->nl();

    $this->openTag($ele);

    // Handle children.
    if ($ele->hasChildNodes()) {
      $this->children($ele->childNodes);
    }

    // If not unary, add a closing tag.
    if (!$this->isUnary($name)) {
      $this->closeTag($ele);
      if ($block) $this->nl();
    }
  }

  protected function text($ele) {
    if ($this->isLiteral($ele)) {
      $this->wr($ele->wholeText);
      return;
    }

    // FIXME: This probably needs some flags set.
    $this->wr(htmlentities($ele->wholeText));

  }

  protected function cdata($ele) {
    $this->wr('<![CDATA[')->wr($ele->wholeText)->wr(']]>');
  }

  protected function comment($ele) {
    $this->wr('<!--')->wr($ele->data)->wr('-->');
  }

  protected function processorInstruction($ele) {
    $this->wr('<?')->wr($ele->target)->wr(' ')->wr($ele->data)->wr(' ?>');
  }

  protected function children($nl) {
    foreach ($nl as $node) {
      $this->node($node);
    }
  }

  protected function openTag($ele) {
    // FIXME: Needs support for SVG, MathML, and namespaced XML.
    $this->wr('<')->wr($ele->tagName);
    $this->attrs($ele);
    $this->wr('>');
  }

  protected function attrs($ele) {
    // FIXME: Needs support for xml, xmlns, xlink, and namespaced elements.
    if (!$ele->hasAttributes()) {
      return $this;
    }

    // TODO: Currently, this always writes name="value", and does not do
    // value-less attributes.
    $map = $ele->attributes;
    $len = $map->length;
    for ($i = 0; $i < $len; ++$i) {
      $node = $map->item($i);
      $this->wr(' ')->wr($node->name)->wr('="')->wr($node->value)->wr('"');
    }
  }

  protected function closeTag($ele) {
    // FIXME: Needs support for SVG, MathML, and namespaced XML.
    $this->wr('</')->wr($ele->tagName)->wr('>');
  }

  protected function wr($text) {
    fwrite($this->out, $text);
    return $this;
  }

  protected function nl() {
    fwrite($this->out, PHP_EOL);
    return $this;
  }

  /**
   * Is an unary tag.
   */
  protected function isUnary($name) {
    return FALSE;
  }

  /**
   * Is block element.
   */
  protected function isBlock($name) {
    return isset(self::$block_elements[$name]);
  }

  protected function isLiteral($element) {
    if (!$element->parentNode) {
      return FALSE;
    }
    return isset(self::$literal_elements[$element->parentNode->tagName]);

  }

}
