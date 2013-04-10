<?php
namespace HTML5\Parser;

class Tokenizer {
  protected $scanner;
  protected $events;
  protected $tok;

  /**
   * Buffer for text.
   */
  protected $text = '';

  public function __construct($scanner, $eventHandler) {
    $this->scanner = $scanner;
    $this->events = $eventHandler;
  }

  /**
   * 8.2.4.1
   */
  public function consumeData() {
    // Scan a token
    $this->scanner->next();
    // Character Ref
    $this->characterReference();

    // TagOpen
    // Null
    // EOF
    // Character
  }

  /**
   * 8.2.4.2
   */
  protected function characterReference($inAttr = FALSE) {
    if ($this->tok == '&') {
      $this->tok = $this->scanner->next();
      $$this->text .= $this->consumeCharacterReference($inAttr);
    }
  }

  protected function consumeCharacterReference($inAttribute = FALSE) {
    $entity = '';
    $start = $this->scanner->position();

    // Whitespace: Ignore
    switch ($this->tok) {
    case NULL:
    case "\t":
    case "\n":
    case "\f":
    case ' ':
    case '&':
    case '<':
      // Don't consume; just return. Spec says return nothing, but I 
      // think we have to append '&' to the string.
      return '&';
    case '#':
      // Consume and read a number
      $this->tok = $this->scanner->next();
      // X[0-9a-fA-F]+;
      // x[0-9a-fA-F]+;
      if ($this->tok == 'x' || $this->tok == 'X') {
        $hex = $this->scanner->getHex();
        $this->tok = $this->scanner->current();
        if (empty($hex)) {
          throw ParseError("Expected &#xHEX;, got &#x" . $this->tok);
        }
        $entity = hexdec($hex);
      }
      // [0-9]+;
      else {
        $entity = $this->scanner->getNumeric();
        $this->tok = $this->scanner->current();
        if (empty($numeric)) {
          throw ParseError("Expected &#DIGITS;, got $#" . $this->tok);
        }
      }
      break;
    default:
      // Attempt to consume a string up to a ';'.
      // [a-zA-Z0-9]+;
      $entity = $this->scanner->getAsciiAlpha();
      $this->tok = $this->scanner->current();

    }

    // We have an entity. We're done here.
    if ($this->tok == ';') {
      return $entity;
    }

    // If in an attribute, then failing to match ; means unconsume the 
    // entire string. Otherwise, failure to match is an error.
    if ($inAttribute) {
      $this->scanner->unconsume($this->scanner->position() - $start);
      return '&';
    }

    throw new ParseError("Expected &ENTITY;, got &ENTITY (no trailing ;)");

  }

  protected function rcdata() {
    // Ampersand
    // <
    // Null
    // EOF
    // Character
  }

  protected function rawtext() {
    // < is a literal
    // NULL is an error
    // EOF
    // Character data
  }

  protected function scriptData() {
    // < is a literal
    // NULL is an error
    // EOF
    // Character data
  }

  /**
   * 8.2.4.7
   */
  protected function plaintext() {
    // NULL -> parse error
    // EOF -> eof
    // -> Character data
  }

  /**
   * 8.2.4.8
   */
  protected function tagOpen() {
    // ! -> markup declaration
    // / -> end tagopen
    // a-zA-Z -> tagname
    // ? -> parse error
    // -> Anything else is a parse error
  }

  /**
   * 8.2.4.9
   */
  protected function endTagOpen() {
    // a-zA-Z -> tagname
    // > -> parse error
    // EOF -> parse error
    // -> parse error
  }

  /**
   * 8.2.4.10
   */
  protected function tagName() {
    // tab, lf, ff, space -> before attr name
    // / -> self-closing tag
    // > -> current tag is done, data-state
    // NULL parse error
    // EOF -> parse error
    // -> append to tagname
  }

  /**
   * 8.2.4.11
   */
  protected function rcdataLessThan() {
    // / -> empty the tmp buffer and go to end-tag
    // ->rcdata
  }

  /**
   * 8.2.4.12
   */
  protected function rcdataEndTag() {
    // A-Za-z: append to tagname
    // -> rcdata state
  }

  /**
   * 8.2.4.13
   */
  protected function rcdataEndTagName() {
    // tab, lf, ff, space -> before attribute or treat as anything
    // / -> self-closing tag
    // > -> end tag, back to data
    // A-Za-z -> append to tagname
    // -> rcdata state
  }

  /**
   * 8.2.4.14
   */
  protected function rawtextLessThan() {
    // / -> rawtext endtag state
    // -> rawtext
  }

  /**
   * 8.2.4.15
   */
  protected function rawtextEndTagOpen() {
    // A-Za-z -> rawtext
    // ->rawtext
  }

  protected function rawtextEndTagName() {
    // tab, lf, ff, space -> before attr name
    //
  }

  protected function scriptLessThan(){
  }
  protected function scriptEndTagOpen() {
  }
  protected function scriptEndTagName() {
  }
  protected function scriptEscapeStart() {
  }
  protected function scriptEscapeStartDash() {
  }
  protected function scriptEscaped() {
  }
  protected function scriptEscapedDash() {
  }
  protected function scriptEscapedDashDash() {
  }
  protected function scriptEscapedLessThan() {
  }
  protected function scriptEscapedEndTagOpen() {
  }
  protected function scriptEscapedEndTagName() {
  }
  protected function scriptDoubleEscapeStart() {
  }
  protected function scriptDoubleEscaped() {
  }
  protected function scriptDoubleEscapedDash() {
  }
  protected function scriptDoubleEscapedDashDash() {
  }
  protected function scriptDoubleEscapedLessThan() {
  }
  protected function scriptDoubleEscapeEnd() {
  }
  protected function beforeAttributeName() {
  }
  protected function attributeName() {
  }
  protected function afterAttributeName() {
  }
  protected function beforeAttributeValue() {
  }
  protected function attributeValueDoubleQuote() {
  }
  protected function attributeValueSingleQuote() {
  }
  protected function attributeValueUnquoted() {
  }
  protected function characterReferenceInAttributeValue() {
  }
  protected function afterAttributeValueQuoted() {
  }
  protected function selfCloseingStartTag() {
  }
  protected function bogusComment() {
  }
  protected function markupDeclarationOpen() {
  }
  protected function commentStart() {
  }
  protected function commentStartDash() {
  }
  protected function comment() {
  }
  protected function commentEndDash() {
  }
  protected function commentEnd() {
  }
  protected function commentEndBangState() {
  }
  protected function doctype() {
  }
  protected function beforeDoctype() {
  }
  protected function doctypeName() {
  }
  protected function afterDoctypeName() {
  }
  protected function doctypePublicKeyword() {
  }
  protected function beforeDoctypePublicId() {
  }
  protected function doctypePublicIdDoubleQuoted() {
  }
  protected function doctypePublicIdSingleQuoted() {
  }
  protected function afterDoctypePublicId() {
  }
  protected function betweenDoctypePublicAndSystem() {
  }
  protected function afterDoctypeSystemKeyword() {
  }
  protected function beforeDoctypeSystemIdentifier() {
  }
  protected function doctypeSystemIdDoubleQuoted() {
  }
  protected function doctypeSystemIdSingleQuoted() {
  }
  protected function afterDoctypeSystemId() {
  }
  protected function bogusDoctype() {
  }
  protected function cdataSection() {
  }





}
