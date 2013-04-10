<?php
namespace HTML5\Parser;

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

  protected function store($event, $data = NULL) {
    $stack[] = array(
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
    $this->store('character', array($cdata));
  }

  public function eof() {
    $this->store('eof');
  }


}
