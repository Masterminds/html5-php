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
    'dd' => 1,
    'dt' => 1,
    'rt' => 1,
    'rp' => 1,
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
    case 'dt':
    case 'dd':
      return $this->handleDT($new, $current);
    case 'rt':
    case 'rp':
      return $this->handleRT($new, $current);

    }

    return $current;
  }

  protected function handleLI($ele, $current) {
    return $this->closeIfCurrentMatches($ele, $current, array('li'));
  }

  protected function handleDT($ele, $current) {
    return $this->closeIfCurrentMatches($ele, $current, array('dt','dd'));
  }
  protected function handleRT($ele, $current) {
    return $this->closeIfCurrentMatches($ele, $current, array('rt','rp'));
  }

  protected function closeIfCurrentMatches($ele, $current, $match) {
    $tname = $current->tagName;
    if (in_array($current->tagName, $match)) {
      $current->parentNode->appendChild($ele);
    }
    else {
      $current->appendChild($ele);
    }
    return $ele;

  }
}
