<?php
namespace HTML5\Parser;

/**
 * Standard events for HTML5.
 *
 * See HTML5 spec section 8.2.4
 */
interface EventHandler {
  public function doctype($name, $publicID, $systemID, $quirks = FALSE);
  public function startTag($name, $attributes = array(), $selfClosing = FALSE);
  public function endTag($name);
  public function comment($cdata);
  public function character($cdata);
  public function eof();

  // Do we need...
  // public function cdata();
  // public function processorInstruction();
}
