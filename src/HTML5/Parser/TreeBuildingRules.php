<?php
namespace HTML5\Parser;

use HTML5\Elements;

/**
 * Handles special-case rules for the DOM tree builder.
 *
 * Many tags have special rules that need to be accomodated on an 
 * individual basis. This class handles those rules.
 *
 * See section 8.1.2.4 of the spec.
 */
class TreeBuildingRules {

  protected static $tags = array(
    'li' => 1,
  );

  /**
   * Build a new rules engine.
   *
   * @param \DOMDocument $doc
   *   The DOM document to use for evaluation and modification.
   */
  public function __construct($doc) {
    $this->doc = $doc;
  }

  /**
   * Returns TRUE if the given tagname has special processing rules.
   */
  public function hasRules($tagname) {
    return isset(self::$tags[$tagname]);
  }

  /**
   * Evaluate the rule for the current tag name.
   *
   * This may modify the existing DOM.
   *
   * @return \DOMElement
   *   The new Current DOM element.
   */
  public function evaluate($new, $current) {

    switch($new->tagName) {
    case 'li':
      return $this->handleLI($new, $current);

    }

    return $current;
  }

  protected function handleLI($ele, $current) {
    if ($current->tagName == 'li') {
      $current->parentNode->appendChild($ele);
      return $ele;
    }
    // XXX FINISH

    $current->appendChild($ele);
    return $current;

  }
}
