<?php
namespace HTML5\Parser;

/**
 * Standard events for HTML5.
 *
 * See HTML5 spec section 8.2.4
 */
interface EventHandler {
  const DOCTYPE_NONE = 0;
  const DOCTYPE_PUBLIC = 1;
  const DOCTYPE_SYSTEM = 2;
  /**
   * A doctype declaration.
   *
   * @param string $name
   *   The name of the root element.
   * @param int $idType
   *   One of DOCTYPE_NONE, DOCTYPE_PUBLIC, or DOCTYPE_SYSTEM.
   * @param string $id
   *   The identifier. For DOCTYPE_PUBLIC, this is the public ID. If DOCTYPE_SYSTEM,
   *   then this is a system ID.
   * @param boolean $quirks
   *   Indicates whether the builder should enter quirks mode.
   */
  public function doctype($name, $idType = 0, $id = NULL, $quirks = FALSE);
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
  public function cdata($data);
  // public function processorInstruction();
}
