<?php
/**
 * @file
 * Test the Tree Builder's special-case rules.
 */
namespace HTML5\Parser;

use HTML5\Elements;

require_once __DIR__ . '/../TestCase.php';

/**
 * These tests are functional, not necessarily unit tests.
 */
class TreeBuildingRulesTest extends \HTML5\Tests\TestCase {

  /**
   * Convenience function for parsing.
   */
  protected function parse($string) {
    $treeBuilder = new DOMTreeBuilder();
    $input = new StringInputStream($string);
    $scanner = new Scanner($input);
    $parser = new Tokenizer($scanner, $treeBuilder);

    $parser->parse();

    return $treeBuilder->document();
  }

  public function testHasRules() {
    $doc = new \DOMDocument('1.0');
    $engine = new TreeBuildingRules($doc);

    $this->assertTrue($engine->hasRules('li'));
    $this->assertFalse($engine->hasRules('imaginary'));
  }

}
