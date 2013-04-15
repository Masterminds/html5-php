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

  public function doctype($name, $type = 0, $id = NULL, $quirks = FALSE) {
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

  public function cdata($data) {
    $this->store('cdata', func_get_args());
  }

  public function text($cdata) {
    //fprintf(STDOUT, "Received TEXT event with: " . $cdata);
    $this->store('text', array($cdata));
  }

  public function eof() {
    $this->store('eof');
  }

  public function parseError($msg, $line, $col) {
    //throw new EventStackParseError(sprintf("%s (line %d, col %d)", $msg, $line, $col));
    //$this->store(sprintf("%s (line %d, col %d)", $msg, $line, $col));
    $this->store('error', func_get_args());
  }


}
class EventStackParseError extends \Exception {
}
