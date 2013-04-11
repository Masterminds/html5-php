<?php
namespace HTML5\Parser;

/**
 * The HTML5 tokenizer.
 *
 * The tokenizer's role is reading data from the scanner and gathering it into
 * semantic units. From the tokenizer, data is emitted to an event handler,
 * which may (for example) create a DOM tree.
 *
 * The HTML5 specification has a detailed explanation of tokenizing HTML5. We
 * follow that specification to the maximum extent that we can. If you find
 * a discrepancy that is not documented, please file a bug and/or submit a
 * patch.
 *
 * This tokenizer is implemented as a recursive descent parser.
 *
 * Within the API documentation, you may see references to the specific section
 * of the HTML5 spec that the code attempts to reproduce. Example: 8.2.4.1.
 * This refers to section 8.2.4.1 of the HTML5 CR specification.
 *
 * @see http://www.w3.org/TR/2012/CR-html5-20121217/
 */
class Tokenizer {
  protected $scanner;
  protected $events;
  protected $tok;

  /**
   * Buffer for text.
   */
  protected $text = '';

  // When this goes to false, the parser stops.
  protected $carryOn = TRUE;

  /**
   * Create a new tokenizer.
   *
   * Typically, parsing a document involves creating a new tokenizer, giving
   * it a scanner (input) and an event handler (output), and then calling
   * the Tokenizer::parse() method.`
   *
   * @param \HTML5\Parser\Scanner $scanner
   *   A scanner initialized with an input stream.
   * @param \HTML5\Parser\EventHandler $eventHandler
   *   An event handler, initialized and ready to receive
   *   events.
   */
  public function __construct($scanner, $eventHandler) {
    $this->scanner = $scanner;
    $this->events = $eventHandler;
  }

  /**
   * Main entry point.
   */
  public function parse() {
    $p = 0;
    do {
      $p = $this->scanner->position();
      $this->consumeData();
    }
    while ($this->carryOn);
  }

  /**
   * Send a TEXT event with the contents of the text buffer.
   *
   * This emits an EventHandler::text() event with the current contents of the
   * temporary text buffer. (The buffer is used to group as much PCDATA
   * as we can instead of emitting lots and lots of TEXT events.)
   */
  protected function flushText() {
    if (empty($this->text)) {
      return;
    }
    $this->events->text($this->text);
    $this->text = '';
  }

  /**
   * Add text to the temporary buffer.
   *
   * @see flushText()
   */
  protected function buffer($str) {
    $this->text .= $str;
  }

  protected function parseError($msg) {
    $line = $this->scanner->currentLine();
    $col = $this->scanner->columnOffset();
    $this->events->parseError($msg, $line, $col);
  }

  /**
   * Consume a character and make a move.
   * HTML5 8.2.4.1
   */
  protected function consumeData() {
    // Character Ref
    $this->characterReference() ||
      $this->tagOpen() ||
      $this->eof() ||
      $this->characterData();

    return $this->carryOn;
  }

  /**
   * This buffers the current token as character data.
   */
  protected function characterData() {
    $tok = $this->scanner->current();

    // This should never happen...
    if ($tok === FALSE) {
      return FALSE;
    }
    // Null
    if ($tok == "\00") {
      $this->parseError("Received NULL character.");
    }
    $this->buffer($tok);
    $this->scanner->next();
    return TRUE;
  }

  protected function eof() {
    if ($this->scanner->current() === FALSE) {
      //fprintf(STDOUT, "EOF");
      $this->flushText();
      $this->events->eof();
      $this->carryOn = FALSE;
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Handle character references (aka entities).
   *
   * HTML5 8.2.4.2
   *
   * @param boolean $inAttribute
   *   Set to TRUE if the text is inside of an attribute value.
   *   FALSE otherwise.
   */
  protected function characterReference($inAttribute = FALSE) {

    // If it fails this, it's definitely not an entity.
    if ($this->scanner->current() != '&') {
      return FALSE;
    }

    // Next char after &.
    $tok = $this->scanner->next();
    $entity = '';
    $start = $this->scanner->position();

    // Whitespace: Ignore
    switch ($tok) {
    case NULL:
    case "\t":
    case "\n":
    case "\f":
    case ' ':
    case '&':
    case '<':
      // Don't consume; just return. Spec says return nothing, but I 
      // think we have to append '&' to the string.
      return FALSE;
    case '#':
      // Consume and read a number
      $tok = $this->scanner->next();

      // Hexidecimal encoding.
      // X[0-9a-fA-F]+;
      // x[0-9a-fA-F]+;
      if ($tok == 'x' || $tok == 'X') {
        $tok = $this->scanner->next(); // Consume x
        $hex = $this->scanner->getHex();
        if (empty($hex)) {
          //throw new ParseError("Expected &#xHEX;, got &#x" . $tok);
          $this->parseError("Expected &#xHEX;, got &#x" . $tok);
          return FALSE;
        }
        $entity = CharacterReference::lookupHex($hex);
      }
      // Decimal encoding.
      // [0-9]+;
      else {
        $numeric = $this->scanner->getNumeric();
        if (empty($numeric)) {
          //throw ParseError("Expected &#DIGITS;, got $#" . $tok);
          $this->parseError("Expected &#DIGITS;, got $#" . $tok);
          return FALSE;
        }
        $entity = CharacterReference::lookupDecimal($numeric);
      }
      break;
    default:
      // Attempt to consume a string up to a ';'.
      // [a-zA-Z0-9]+;
      $cname = $this->scanner->getAsciiAlpha();
      $entity = CharacterReference::lookupName($cname);
      if ($entity == NULL) {
          $this->parseError("No match in entity table for " . $entity);
      }

    }
    // The scanner has advanced the cursor for us.
    $tok = $this->scanner->current();

    // We have an entity. We're done here.
    if ($tok == ';') {
      $this->buffer($entity);
      $this->scanner->next();
      return TRUE;
    }

    // If in an attribute, then failing to match ; means unconsume the 
    // entire string. Otherwise, failure to match is an error.
    if ($inAttribute) {
      $this->scanner->unconsume($this->scanner->position() - $start);
      $this->buffer('&');
      return FALSE;
    }

    //throw new ParseError("Expected &ENTITY;, got &ENTITY (no trailing ;) " . $tok);
    $this->parseError("Expected &ENTITY;, got &ENTITY (no trailing ;) " . $tok);

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
    //fprintf(STDOUT, '+');
    return FALSE;
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
