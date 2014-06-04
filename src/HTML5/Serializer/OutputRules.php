<?php
/**
 * @file
 * The rules for generating output in the serializer.
 *
 * These output rules are likely to generate output similar to the document that
 * was parsed. It is not intended to output exactly the document that was parsed.
 */
namespace HTML5\Serializer;

use \HTML5\Elements;

/**
 * Generate the output html5 based on element rules.
 */
class OutputRules implements \HTML5\Serializer\RulesInterface {

  const IM_IN_HTML = 1;
  const IM_IN_SVG = 2;
  const IM_IN_MATHML = 3;

  protected $traverser;
  protected $encode = FALSE;
  protected $out;
  protected $outputMode;

  const DOCTYPE = '<!DOCTYPE html>';

  public function __construct($output, $options = array()) {

    if (isset($options['encode_entities'])) {
      $this->encode = $options['encode_entities'];
    }

    $this->outputMode = static::IM_IN_HTML;
    $this->out = $output;
  }

  public function setTraverser(\HTML5\Serializer\Traverser $traverser) {
    $this->traverser = $traverser;

    return $this;
  }

  public function document($dom) {
    $this->doctype();
    $this->traverser->node($dom->documentElement);
    $this->nl();
  }

  protected function doctype() {
    $this->wr(static::DOCTYPE);
    $this->nl();
  }

  public function element($ele) {
    $name = $ele->tagName;

    // Per spec:
    // If the element has a declared namespace in the HTML, MathML or
    // SVG namespaces, we use the lname instead of the tagName.
    if ($this->traverser->isLocalElement($ele)) {
      $name = $ele->localName;
    }

    // If we are in SVG or MathML there is special handling.
    // Using if/elseif instead of switch because it's faster in PHP.
    if ($name == 'svg') {
        $this->outputMode = static::IM_IN_SVG;
        $name = Elements::normalizeSvgElement($name);
    }
    elseif ($name == 'math') {
      $this->outputMode = static::IM_IN_MATHML;
    }

    $this->openTag($ele);

    // Handle children.
    if ($ele->hasChildNodes()) {
      $this->traverser->children($ele->childNodes);
    }

    // Close out the SVG or MathML special handling.
    if ($name == 'svg' || $name == 'math') {
      $this->outputMode = static::IM_IN_HTML;
    }

    // If not unary, add a closing tag.
    if (!Elements::isA($name, Elements::VOID_TAG)) {
      $this->closeTag($ele);
    }
  }

  /**
   * Write a text node.
   *
   * @param \DOMText $ele
   *   The text node to write.
   */
  public function text($ele) {
    if (isset($ele->parentNode) && isset($ele->parentNode->tagName) && Elements::isA($ele->parentNode->localName, Elements::TEXT_RAW)) {
      $this->wr($ele->data);
      return;
    }

    // FIXME: This probably needs some flags set.
    $this->wr($this->enc($ele->data));

  }

  public function cdata($ele) {
    // This encodes CDATA.
    $this->wr($ele->ownerDocument->saveXML($ele));
  }

  public function comment($ele) {
    // These produce identical output.
    //$this->wr('<!--')->wr($ele->data)->wr('-->');
    $this->wr($ele->ownerDocument->saveXML($ele));
  }

  public function processorInstruction($ele) {
    $this->wr('<?')->wr($ele->target)->wr(' ')->wr($ele->data)->wr('?>');
  }

  /**
   * Write the opening tag.
   *
   * Tags for HTML, MathML, and SVG are in the local name. Otherwise, use the
   * qualified name (8.3).
   *
   * @param \DOMNode $ele
   *   The element being written.
   */
  protected function openTag($ele) {
    $this->wr('<')->wr($this->traverser->isLocalElement($ele)?$ele->localName:$ele->tagName);
    $this->attrs($ele);

    if ($this->outputMode == static::IM_IN_HTML) {
      $this->wr('>');
    }
    // If we are not in html mode we are in SVG, MathML, or XML embedded content.
    else {
      if ($ele->hasChildNodes()) {
        $this->wr('>');
      }
      // If there are no children this is self closing.
      else {
        $this->wr(' />');
      }
    }
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
      $val = $this->enc($node->value, TRUE);

      // XXX: The spec says that we need to ensure that anything in
      // the XML, XMLNS, or XLink NS's should use the canonical
      // prefix. It seems that DOM does this for us already, but there
      // may be exceptions.
      $name = $node->name;

      // Special handling for attributes in SVG and MathML.
      // Using if/elseif instead of switch because it's faster in PHP.
      if ($this->outputMode == static::IM_IN_SVG) {
        $name = Elements::normalizeSvgAttribute($name);
      }
      elseif ($this->outputMode == static::IM_IN_MATHML) {
        $name = Elements::normalizeMathMlAttribute($name);
      }

      $this->wr(' ')->wr($name);
      if (isset($val) && $val !== '') {
        $this->wr('="')->wr($val)->wr('"');
      }
    }
  }

  /**
   * Write the closing tag.
   *
   * Tags for HTML, MathML, and SVG are in the local name. Otherwise, use the
   * qualified name (8.3).
   *
   * @param \DOMNode $ele
   *   The element being written.
   */
  protected function closeTag($ele) {
    if ($this->outputMode == static::IM_IN_HTML || $ele->hasChildNodes()) {
      $this->wr('</')->wr($this->traverser->isLocalElement($ele)?$ele->localName:$ele->tagName)->wr('>');
    }
  }

  /**
   * Write to the output.
   *
   * @param string $text
   *   The string to put into the output.
   *
   * @return HTML5\Serializer\Traverser
   *   $this so it can be used in chaining.
   */
  protected function wr($text) {
    fwrite($this->out, $text);
    return $this;
  }

  /**
   * Write a new line character.
   *
   * @return HTML5\Serializer\Traverser
   *   $this so it can be used in chaining.
   */
  protected function nl() {
    fwrite($this->out, PHP_EOL);
    return $this;
  }

  /**
   * Encode text.
   *
   * When encode is set to FALSE, the default value, the text passed in is
   * escaped per section 8.3 of the html5 spec. For details on how text is
   * escaped see the escape() method.
   *
   * When encoding is set to true the text is converted to named character
   * references where appropriate. Section 8.1.4 Character references of the
   * html5 spec refers to using named character references. This is useful for
   * characters that can't otherwise legally be used in the text.
   *
   * The named character references are listed in section 8.5.
   *
   * @see http://www.w3.org/TR/2013/CR-html5-20130806/syntax.html#named-character-references
   *
   * True encoding will turn all named character references into their entities.
   * This includes such characters as +.# and many other common ones. By default
   * encoding here will just escape &'<>".
   *
   * Note, PHP 5.4+ has better html5 encoding.
   *
   * @todo Use the Entities class in php 5.3 to have html5 entities.
   *
   * @param string $text
   *   text to encode.
   * @param boolean $attribute
   *   True if we are encoding an attrubute, false otherwise
   *
   * @return string
   *   The encoded text.
   */
  protected function enc($text, $attribute = FALSE) {

    // Escape the text rather than convert to named character references.
    if (!$this->encode) {
      return $this->escape($text, $attribute);
    }

    // If we are in PHP 5.4+ we can use the native html5 entity functionality to
    // convert the named character references.
    if (defined('ENT_HTML5')) {
      return htmlentities($text, ENT_HTML5 | ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8', FALSE);
    }
    // If a version earlier than 5.4 html5 entities are not entirely handled.
    // This manually handles them.
    else {
      return strtr($text, \HTML5\Serializer\HTML5Entities::$map);
    }
  }

  /**
   * Escape test.
   *
   * According to the html5 spec section 8.3 Serializing HTML fragments, text
   * within tags that are not style, script, xmp, iframe, noembed, and noframes
   * need to be properly escaped.
   *
   * The & should be converted to &amp;, no breaking space unicode characters
   * converted to &nbsp;, when in attribute mode the " should be converted to
   * &quot;, and when not in attribute mode the < and > should be converted to
   * &lt; and &gt;.
   *
   * @see http://www.w3.org/TR/2013/CR-html5-20130806/syntax.html#escapingString
   *
   * @param string $text
   *   text to escape.
   * @param boolean $attribute
   *   True if we are escaping an attrubute, false otherwise
   */
  protected function escape($text, $attribute = FALSE) {

    // Not using htmlspecialchars because, while it does escaping, it doesn't
    // match the requirements of section 8.5. For example, it doesn't handle
    // non-breaking spaces.
    if ($attribute) {
      $replace = array('"'=>'&quot;', '&'=>'&amp;', "\xc2\xa0"=>'&nbsp;');
    }
    else {
      $replace = array('<'=>'&lt;', '>'=>'&gt;', '&'=>'&amp;', "\xc2\xa0"=>'&nbsp;');
    }

    return strtr($text, $replace);
  }
}
