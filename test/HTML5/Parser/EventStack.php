<?php
namespace HTML5\Parser;

/**
 * This testing class gathers events from a parser and builds a stack of events.
 * It is useful for checking the output of a tokenizer.
 */
class EventStack implements EventHandler {
  protected $stack;

  public function __construct() {
    $this->stack = array();
  }

  /**
   * Get the event stack.
   */
  public function events() {
    return $this->stack;
  }

  public function depth() {
    return count($this->stack);
  }

  public function get($index) {
    return $this->stack[$index];
  }

  protected function store($event, $data = NULL) {
    $this->stack[] = array(
      'name' => $event,
      'data' => $data,
    );
  }

  public function doctype($name, $publicId, $systemID, $quirks = FALSE) {
    $args = func_get_args();
    $this->store('doctype', $args);
  }

  public function startTag($name, $attributes = array(), $selfClosing = FALSE) {
    $args = func_get_args();
    $this->store('startTag', $args);
  }

  public function endTag($name) {
    $this->store('endTag', array($name));
  }

  public function comment($cdata) {
    $this->store('comment', array($cdata));
  }

  public function text($cdata) {
    //fprintf(STDOUT, "Received TEXT event with: " . $cdata);
    $this->store('text', array($cdata));
  }

  public function eof() {
    $this->store('eof');
  }


}
