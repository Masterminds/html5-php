<?php
namespace HTML5\Parser;

/**
 * Standard events for HTML5.
 *
 * See HTML5 spec section 8.2.4
 */
interface EventHandler {
  /**
   * A doctype declaration.
   */
  public function doctype($name, $publicID, $systemID, $quirks = FALSE);
  /**
   * A start tag.
   */
  public function startTag($name, $attributes = array(), $selfClosing = FALSE);
  /**
   * An end-tag.
   */
  public function endTag($name);
  /**
   * A comment section (unparsed character data).
   */
  public function comment($cdata);
  /**
   * A unit of parsed character data.
   *
   * Entities in this text are *already decoded*.
   */
  public function text($cdata);
  /**
   * Indicates that the document has been entirely processed.
   */
  public function eof();
  /**
   * Emitted when the parser encounters an error condition.
   */
  public function parseError($msg, $line, $col);

  // Do we need...
  // public function cdata();
  // public function processorInstruction();
}
