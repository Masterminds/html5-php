<?php
namespace HTML5\Parser;

class DOMTreeBuilder implements EventHandler {
  public function doctype($name, $idType = 0, $id = NULL, $quirks = FALSE) {
  }
  public function startTag($name, $attributes = array(), $selfClosing = FALSE) {
  }
  public function endTag($name) {
  }
  public function comment($cdata) {
  }
  public function text($cdata) {
  }
  public function eof() {
  }
  public function parseError($msg, $line, $col) {
  }
  public function cdata($data) {
  }
  public function processingInstruction($name, $data = NULL) {
  }
}
