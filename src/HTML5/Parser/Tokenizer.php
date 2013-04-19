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

  const WHITE="\t\n\f ";

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

      // FIXME: Add infinite loop protection.
    }
    while ($this->carryOn);
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
    if ($tok === "\00") {
      $this->parseError("Received NULL character.");
    }
    // fprintf(STDOUT, "Writing '%s'", $tok);
    $this->buffer($tok);
    $this->scanner->next();
    return TRUE;
  }

  protected function eof() {
    if ($this->scanner->current() === FALSE) {
      //fprintf(STDOUT, "EOF");
      $this->flushBuffer();
      $this->events->eof();
      $this->carryOn = FALSE;
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Handle character references (aka entities).
   *
   * This version is specific to PCDATA, as it buffers data into the
   * text buffer. For a generic version, see decodeCharacterReference().
   *
   * HTML5 8.2.4.2
   */
  protected function characterReference() {
    $ref = $this->decodeCharacterReference();
    if ($ref !== FALSE) {
      $this->buffer($ref);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Decode a character reference and return the string.
   *
   * Returns FALSE if the entity could not be found. If $inAttribute is set
   * to TRUE, a bare & will be returned as-is.
   *
   * @param boolean $inAttribute
   *   Set to TRUE if the text is inside of an attribute value.
   *   FALSE otherwise.
   */
  protected function decodeCharacterReference($inAttribute = FALSE) {

    // If it fails this, it's definitely not an entity.
    if ($this->scanner->current() != '&') {
      return FALSE;
    }

    // Next char after &.
    $tok = $this->scanner->next();
    $entity = '';
    $start = $this->scanner->position();

    if ($tok == FALSE) {
      return '&';
    }

    // These indicate not an entity. We return just
    // the &.
    if (strspn($tok, self::WHITE . "&<") == 1) {
      //$this->scanner->next();
      return '&';
    }

    // Numeric entity
    if ($tok == '#') {
      $tok = $this->scanner->next();

      // Hexidecimal encoding.
      // X[0-9a-fA-F]+;
      // x[0-9a-fA-F]+;
      if ($tok == 'x' || $tok == 'X') {
        $tok = $this->scanner->next(); // Consume x

        // Convert from hex code to char.
        $hex = $this->scanner->getHex();
        if (empty($hex)) {
          $this->parseError("Expected &#xHEX;, got &#x%s", $tok);
          // We unconsume because we don't know what parser rules might
          // be in effect for the remaining chars. For example. '&#>'
          // might result in a specific parsing rule inside of tag
          // contexts, while not inside of pcdata context.
          $this->scanner->unconsume(2);
          return '&';
        }
        $entity = CharacterReference::lookupHex($hex);
      }
      // Decimal encoding.
      // [0-9]+;
      else {
        // Convert from decimal to char.
        $numeric = $this->scanner->getNumeric();
        if ($numeric === FALSE) {
          $this->parseError("Expected &#DIGITS;, got &#%s", $tok);
          $this->scanner->unconsume(2);
          return '&';
        }
        $entity = CharacterReference::lookupDecimal($numeric);
      }
    }
    // String entity.
    else {
      // Attempt to consume a string up to a ';'.
      // [a-zA-Z0-9]+;
      $cname = $this->scanner->getAsciiAlpha();
      $entity = CharacterReference::lookupName($cname);
      if ($entity == NULL) {
          $this->parseError("No match in entity table for '%s'", $entity);
      }
    }

    // The scanner has advanced the cursor for us.
    $tok = $this->scanner->current();

    // We have an entity. We're done here.
    if ($tok == ';') {
      $this->scanner->next();
      return $entity;
    }

    // If in an attribute, then failing to match ; means unconsume the 
    // entire string. Otherwise, failure to match is an error.
    if ($inAttribute) {
      $this->scanner->unconsume($this->scanner->position() - $start);
      return '&';
    }

    $this->parseError("Expected &ENTITY;, got &ENTITY%s (no trailing ;) ", $tok);
    return '&' . $entity;

  }

  /**
   * 8.2.4.8
   */
  protected function tagOpen() {
    if ($this->scanner->current() != '<') {
      return FALSE;
    }

    // Any buffered text data can go out now.
    $this->flushBuffer();

    $this->scanner->next();

    return $this->markupDeclaration() ||
      $this->endTag() ||
      $this->processingInstruction() ||
      $this->tagName() ||
      // This always returns false.
      $this->parseError("Illegal tag opening") ||
      $this->characterData();
  }

  protected function markupDeclaration() {
    if ($this->scanner->current() != '!') {
      return FALSE;
    }

    $tok = $this->scanner->next();

    // Comment:
    if ($tok == '-' && $this->scanner->peek() == '-') {
      $this->scanner->next(); // Consume the other '-'
      $this->scanner->next(); // Next char.
      return $this->comment();
    }
    // Doctype
    elseif($tok == 'D') {
      return $this->doctype('');
    }
    // CDATA section
    elseif($tok == '[') {
      return $this->cdataSection();
    }

    // FINISH
    $this->parseError("Expected <!--, <![CDATA[, or <!DOCTYPE. Got <!%s", $tok);
    $this->bogusComment('<!');
    return TRUE;
  }

  /**
   * Consume an end tag.
   * 8.2.4.9
   */
  protected function endTag() {
    if ($this->scanner->current() != '/') {
      return FALSE;
    }
    $tok = $this->scanner->next();

    // a-zA-Z -> tagname
    // > -> parse error
    // EOF -> parse error
    // -> parse error
    if (!ctype_alpha($tok)) {
      $this->parseError("Expected tag name, got '%s'", $tok);
      if ($tok == "\0" || $tok === FALSE) {
        return FALSE;
      }
      return $this->bogusComment('</');
    }

    $name = $this->scanner->charsUntil("\n\f \t>");
    // Trash whitespace.
    $this->scanner->whitespace();

    if ($this->scanner->current() != '>') {
      $this->parseError("Expected >, got '%s'", $this->scanner->current());
      // We just trash stuff until we get to the next tag close.
      $this->scanner->charsUntil('>');
    }

    $this->events->endTag($name);
    $this->scanner->next();
    return TRUE;

  }

  /**
   * Consume a tag name and body.
   * 8.2.4.10
   */
  protected function tagName() {
    $tok = $this->scanner->current();
    if (!ctype_alpha($tok)) {
      return FALSE;
    }

    // We know this is at least one char.
    $name = strtolower($this->scanner->charsUntil("/> \n\f\t"));
    $attributes = array();
    $selfClose = FALSE;

    do {
      $this->scanner->whitespace();
      $this->attribute($attributes);
    }
    while (!$this->isTagEnd($selfClose));

    $this->events->startTag($name, $attributes, $selfClose);
    // Should we do this? What does this buy that selfClose doesn't?
    if ($selfClose) {
      $this->events->endTag($name);
    }

    $this->scanner->next();

    return TRUE;
  }

  protected function isTagEnd(&$selfClose) {
    $tok = $this->scanner->current();
    if ($tok == '/') {
      $this->scanner->next();
      $this->scanner->whitespace();
      if ($this->scanner->current() == '>') {
        $selfClose = TRUE;
        $this->scanner->next();
        return TRUE;
      }
      if ($this->scanner->current() === FALSE) {
        $this->parseError("Unexpected EOF inside of tag.");
        return TRUE;
      }
      // Basically, we skip the / token and go on.
      // See 8.2.4.43.
      $this->parseError("Unexpected '%s' inside of a tag.", $this->scanner->current());
      return FALSE;
    }

    if ($this->scanner->current() == '>') {
      return TRUE;
    }
    if ($this->scanner->current() === FALSE) {
      $this->parseError("Unexpected EOF inside of tag.");
      return TRUE;
    }

    return FALSE;
  }


  /**
   * Parse attributes from inside of a tag.
   */
  protected function attribute(&$attributes) {
    $tok = $this->scanner->current();
    if ($tok == '/' || $tok == '>' || $tok === FALSE) {
      return FALSE;
    }

    $name = strtolower($this->scanner->charsUntil("/>=\n\f\t "));

    if (strlen($name) == 0) {
      $this->parseError("Expected an attribute name, got %s.", $this->scanner->current());
      // Really, only '=' can be the char here. Everything else gets absorbed
      // under one rule or another.
      $name = $this->scanner->current();
      $this->scanner->next();
    }
    if (preg_match('/[\'\"]/', $name)) {
    //if (strspn($name, '\'\"')) {
      $this->parseError("Unexpected characters in attribute name: %s", $name);
    }
    // Whitespace not allowed between name and =.
    //$this->scanner->whitespace();

    $val = $this->attributeValue();
    //return array($name, $val);
    $attributes[$name] = $val;
    return TRUE;
  }

  /**
   * Consume an attribute value.
   * 8.2.4.37 and after.
   */
  protected function attributeValue() {
    if ($this->scanner->current() != '=') {
      return NULL;
    }
    $this->scanner->next();
    // Whitespace is significant
    //$this->scanner->whitespace();

    $tok = $this->scanner->current();
    switch ($tok) {
    case "\n":
    case "\f":
    case " ":
    case "\t":
      // Whitespace here indicates an empty value.
      return NULL;
    case '"':
    case "'":
      $this->scanner->next();
      return $this->quotedAttributeValue($tok);
    case '>':
    // case '/': // 8.2.4.37 seems to allow foo=/ as a valid attr.
      $this->parseError("Expected attribute value, got tag end.");
      return NULL;
    case '=':
    case '`':
      $this->parseError("Expecting quotes, got %s.", $tok);
      return $this->unquotedAttributeValue();
    default:
      return $this->unquotedAttributeValue();
    }
  }

  /**
   * Get an attribute value string.
   *
   * @param string $quote
   *   IMPORTANT: This is a series of chars! Any one of which will be considered
   *   termination of an attribute's value. E.g. "\"'" will stop at either
   *   ' or ".
   * @return string
   *   The attribute value.
   */
  protected function quotedAttributeValue($quote) {
    $stoplist = "\t\n\f" . $quote;
    $val = '';
    $tok = $this->scanner->current();
    while (strspn($tok, $stoplist) == 0 && $tok !== FALSE) {
      if ($tok == '&') {
        $val .= $this->decodeCharacterReference(TRUE);
        $tok = $this->scanner->current();
      }
      else {
        $val .= $tok;
        $tok = $this->scanner->next();
      }
    }
    $this->scanner->next();
    return $val;
  }
  protected function unquotedAttributeValue() {
    $stoplist = "\t\n\f >";
    $val = '';
    $tok = $this->scanner->current();
    while (strspn($tok, $stoplist) == 0 && $tok !== FALSE) {
      if ($tok == '&') {
        $val .= $this->decodeCharacterReference(TRUE);
      }
      else {
        if(strspn($tok, "\"'<=`") > 0) {
          $this->parseError("Unexpected chars in unquoted attribute value %s", $tok);
        }
        $val .= $tok;
        $tok = $this->scanner->next();
      }
    }
    return $val;
  }


  /**
   * Consume malformed markup as if it were a comment.
   * 8.2.4.44
   *
   * The spec requires that the ENTIRE tag-like thing be enclosed inside of
   * the comment. So this will generate comments like:
   *
   * &lt;!--&lt/+foo&gt;--&gt;
   *
   * @param string $leading
   *   Prepend any leading characters. This essentially
   *   negates the need to backtrack, but it's sort of
   *   a hack.
   */
  protected function bogusComment($leading = '') {

    // TODO: This can be done more efficiently when the
    // scanner exposes a readUntil() method.
    $comment = $leading;
    $tok = $this->scanner->current();
    do {
      $comment .= $tok;
      $tok = $this->scanner->next();
    } while ($tok !== FALSE && $tok != '>');

    $this->flushBuffer();
    $this->events->comment($comment . $tok);
    $this->scanner->next();

    return TRUE;
  }

  /**
   * Read a comment.
   *
   * Expects the first tok to be inside of the comment.
   */
  protected function comment() {
    $tok = $this->scanner->current();
    $comment = '';

    // <!-->. Emit an empty comment because 8.2.4.46 says to.
    if ($tok == '>') {
      // Parse error. Emit the comment token.
      $this->parseError("Expected comment data, got '>'");
      $this->events->comment('');
      $this->scanner->next();
      return TRUE;
    }

    // Replace NULL with the replacement char.
    if ($tok == "\0") {
      $tok = UTF8Utils::FFFD;
    }
    while (!$this->isCommentEnd()) {
      $comment .= $tok;
      $tok = $this->scanner->next();
    }

    $this->events->comment($comment);
    $this->scanner->next();
    return TRUE;
  }

  protected function isCommentEnd() {
    // EOF
    if($this->scanner->current() === FALSE) {
      // Hit the end.
      $this->parseError("Unexpected EOF in a comment.");
      return TRUE;
    }

    // If it doesn't start with -, not the end.
    if($this->scanner->current() != '-') {
      return FALSE;
    }


    // Advance one, and test for '->'
    if ($this->scanner->next() == '-'
        && $this->scanner->peek() == '>') {
      $this->scanner->next(); // Consume the last '>'
      return TRUE;
    }
    // Unread '-';
    $this->scanner->unconsume(1);
    return FALSE;
  }

  /**
   * Parse a DOCTYPE.
   *
   * Parse a DOCTYPE declaration. This method has strong bearing on whether or
   * not Quirksmode is enabled on the event handler.
   *
   * @todo This method is a little long. Should probably refactor.
   */
  protected function doctype() {
    if ($this->scanner->current() != 'D') {
      return FALSE;
    }
    // Check that string is DOCTYPE
    $chars = $this->scanner->charsWhile("DOCTYPE");
    if ($chars != 'DOCTYPE') {
      $this->parseError('Expected DOCTYPE, got %s', $chars);
      return $this->bogusComment('<!' .  $chars);
    }

    $this->scanner->whitespace();
    $tok = $this->scanner->current();

    // EOF: die.
    if ($tok === FALSE) {
      $this->events->doctype('html5',EventHandler::DOCTYPE_NONE,'', TRUE);
      return $this->eof();
    }

    $doctypeName = '';

    // NULL char: convert.
    if ($tok === "\0") {
      $this->parseError("Unexpected NULL character in DOCTYPE.");
      $doctypeName .= UTF8::FFFD;
      $tok = $this->scanner->next();
    }

    $stop = " \n\f>";
    $doctypeName = $this->scanner->charsUntil($stop);
    // Lowercase ASCII, replace \0 with FFFD
    $doctypeName = strtolower(strtr($doctypeName, "\0", UTF8Utils::FFFD));

    $tok = $this->scanner->current();

    // If FALSE, emit a parse error, DOCTYPE, and return.
    if ($tok === FALSE) {
      $this->parseError('Unexpected EOF in DOCTYPE declaration.');
      $this->events->doctype($doctypeName, EventHandler::DOCTYPE_NONE, NULL, TRUE);
      return TRUE;
    }

    // Short DOCTYPE, like <!DOCTYPE html>
    if ($tok == '>') {
      // DOCTYPE without a name.
      if (strlen($doctypeName) == 0) {
        $this->parseError("Expected a DOCTYPE name. Got nothing.");
        $this->events->doctype($doctypeName, 0, NULL, TRUE);
        $this->scanner->next();
        return TRUE;
      }
      $this->events->doctype($doctypeName);
      $this->scanner->next();
      return TRUE;
    }
    $this->scanner->whitespace();

    $pub = strtoupper($this->scanner->getAsciiAlpha());
    $white = strlen($this->scanner->whitespace());
    $tok = $this->scanner->current();

    // Get ID, and flag it as pub or system.
    if (($pub == 'PUBLIC' || $pub == 'SYSTEM') && $white > 0) {
      // Get the sys ID.
      $type = $pub == 'PUBLIC' ? EventHandler::DOCTYPE_PUBLIC : EventHandler::DOCTYPE_SYSTEM;
      $id = $this->quotedString("\0>");
      if ($id === FALSE) {
        $this->events->doctype($doctypeName, $type, $pub, FALSE);
        return FALSE;
      }

      // Premature EOF.
      if ($this->scanner->current() === FALSE) {
        $this->parseError("Unexpected EOF in DOCTYPE");
        $this->events->doctype($doctypeName, $type, $id, TRUE);
        return TRUE;
      }

      // Well-formed complete DOCTYPE.
      $this->scanner->whitespace();
      if ($this->scanner->current() == '>') {
        $this->events->doctype($doctypeName, $type, $id, FALSE);
        $this->scanner->next();
        return TRUE;
      }

      // If we get here, we have <!DOCTYPE foo PUBLIC "bar" SOME_JUNK
      // Throw away the junk, parse error, quirks mode, return TRUE.
      $this->scanner->charsUntil(">");
      $this->parseError("Malformed DOCTYPE.");
      $this->events->doctype($doctypeName, $type, $id, TRUE);
      $this->scanner->next();
      return TRUE;
    }

    // Else it's a bogus DOCTYPE.
    // Consume to > and trash.
    $this->scanner->charsUntil('>');

    $this->parseError("Expected PUBLIC or SYSTEM. Got %s.", $pub);
    $this->events->doctype($doctypeName, 0, NULL, TRUE);
    $this->scanner->next();
    return TRUE;

  }

  /**
   * Utility for reading a quoted string.
   *
   * @param string $stopchars
   *   Characters (in addition to a close-quote) that should stop the string.
   *   E.g. sometimes '>' is higher precedence than '"' or "'".
   * @return mixed
   *   String if one is found (quotations omitted)
   */
  protected function quotedString($stopchars) {
    $tok = $this->scanner->current();
    if ($tok == '"' || "'") {
      $this->scanner->next();
      $ret = $this->scanner->charsUntil($tok . $stopchars);
      if ($this->scanner->current() == $tok) {
        $this->scanner->next();
      }
      else {
        // Parse error because no close quote.
        $this->parseError("Expected %s, got %s", $tok, $this->scanner->current());
      }
      return $ret;
    }
    return FALSE;
  }


  /**
   * Handle a CDATA section.
   */
  protected function cdataSection() {
    if ($this->scanner->current() != '[') {
      return FALSE;
    }
    $cdata = '';
    $this->scanner->next();

    $chars = $this->scanner->charsWhile('CDAT');
    if ($chars != 'CDATA' || $this->scanner->current() != '[') {
      $this->parseError('Expected [CDATA[, got %s', $chars);
      return $this->bogusComment('<![' . $chars);
    }

    $tok = $this->scanner->next();
    do {
      if ($tok === FALSE) {
        $this->parseError('Unexpected EOF inside CDATA.');
        $this->bogusComment('<![CDATA[' . $cdata);
        return TRUE;
      }
      $cdata .= $tok;
      $tok = $this->scanner->next();
    }
    while (!$this->isCdataClose());

    $this->scanner->next(); // consume >
    $this->scanner->next(); // Next char after >

    $this->events->cdata($cdata);
    return TRUE;

  }
  /**
   * Check whether the parser has reached the end of a CDATA section.
   */
  protected function isCdataClose() {
    $tok = $this->scanner->current();
    if ($tok != ']') {
      return FALSE;
    }
    $tok = $this->scanner->next();
    if ($tok == ']' && $this->scanner->peek() == '>') {
      return TRUE;
    }
    // Unconsume one char and return.
    $this->scanner->unconsume();
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


  // ================================================================
  // Non-HTML5
  // ================================================================
  /**
   * Handle a processing instruction.
   *
   * XML processing instructions are supposed to be ignored in HTML5,
   * treated as "bogus comments". However, since we're not a user
   * agent, we allow them. We consume until ?> and then issue a 
   * EventListener::processingInstruction() event.
   */
  protected function processingInstruction() {
    if ($this->scanner->current() != '?') {
      return FALSE;
    }

    $tok = $this->scanner->next();
    $procName = $this->scanner->getAsciiAlpha();
    $white = strlen($this->scanner->whitespace());

    // If not a PI, send to bogusComment.
    if (strlen($procName) == 0 || $white == 0 || $this->scanner->current() == FALSE) {
      $this->parseError("Expected processing instruction name, got $tok");
      $this->bogusComment('<?' . $tok . $procName);
      return TRUE;
    }

    $data = '';
    while ($this->scanner->current() != '?' && $this->scanner->peek() != '>') {
      $data .= $this->scanner->current();

      $tok = $this->scanner->next();
      if ($tok === FALSE) {
        $this->parseError("Unexpected EOF in processing instruction.");
        $this->events->processingInstruction($procName, $data);
        return TRUE;
      }

    }

    $this->scanner->next(); // >
    $this->scanner->next(); // Next token.
    $this->events->processingInstruction($procName, $data);
    return TRUE;
  }


  // ================================================================
  // UTILITY FUNCTIONS
  // ================================================================

  /**
   * Send a TEXT event with the contents of the text buffer.
   *
   * This emits an EventHandler::text() event with the current contents of the
   * temporary text buffer. (The buffer is used to group as much PCDATA
   * as we can instead of emitting lots and lots of TEXT events.)
   */
  protected function flushBuffer() {
    if (empty($this->text)) {
      return;
    }
    $this->events->text($this->text);
    $this->text = '';
  }

  /**
   * Add text to the temporary buffer.
   *
   * @see flushBuffer()
   */
  protected function buffer($str) {
    $this->text .= $str;
  }

  /**
   * Emit a parse error.
   *
   * A parse error always returns FALSE because it never consumes any 
   * characters.
   */
  protected function parseError($msg) {
    $args = func_get_args();

    if (count($args) > 1) {
      array_shift($args);
      $msg = vsprintf($msg, $args);
    }

    $line = $this->scanner->currentLine();
    $col = $this->scanner->columnOffset();
    $this->events->parseError($msg, $line, $col);
    return FALSE;
  }

}
