<?php
/**
 * @file
 * Test the Scanner. This requires the InputStream tests are all good.
 */
namespace HTML5\Tests;

use \HTML5\InputStream;
use \HTML5\Parser\Scanner;

require_once 'TestCase.php';

class ScannerTest extends TestCase {

  /**
   * A canary test to make sure the basics are setup and working.
   */
  public function testConstruct() {
    $is = new InputStream("abc");
    $s = new Scanner($is);

    $this->assertInstanceOf('\HTML5\Parser\Scanner', $s);
  }

  public function testNext() {
    $s = new Scanner(new InputStream("abc"));

    $this->assertEquals('a', $s->next());
    $this->assertEquals('b', $s->next());
    $this->assertEquals('c', $s->next());
  }

  public function testPosition() {
    $s = new Scanner(new InputStream("abc"));

    $this->assertEquals(0, $s->position());

    $s->next();
    $this->assertEquals(1, $s->position());
  }

  public function testPeek() {
    $s = new Scanner(new InputStream("abc"));

    // The scanner is currently pointed before a.
    $this->assertEquals('b', $s->peek());

    $s->next();
    $this->assertEquals('c', $s->peek());
  }

  public function testCurrent() {
    $s = new Scanner(new InputStream("abc"));

    // Before scanning the string begins the current is empty.
    $this->assertEquals('', $s->current());

    $c = $s->next();
    $this->assertEquals($c, $s->current());

    // Test movement through the string.
    $c = $s->next();
    $this->assertEquals($c, $s->current());
  }

  public function testUnconsume() {
    $s = new Scanner(new InputStream("abcdefghijklmnopqrst"));

    // Get initial position.
    $s->next();
    $start = $s->position();

    // Move forward a bunch of positions.
    $amount = 7;
    for($i = 0; $i < $amount; $i++) {
      $s->next();
    }

    // Roll back the amount we moved forward.
    $s->unconsume($amount);

    $this->assertEquals($start, $s->position());
  }

  public function testGetHex() {
    $s = new Scanner(new InputStream("ab13ck45DE*"));

    $this->assertEquals('ab13c', $s->getHex());

    $s->next();
    $this->assertEquals('45DE', $s->getHex());
  }
  
  public function testGetAsciiAlpha() {
    $s = new Scanner(new InputStream("abcdef1%mnop*"));

    $this->assertEquals('abcdef', $s->getAsciiAlpha());

    // Move past the 1% to scan the next group of text.
    $s->next();
    $s->next();
    $this->assertEquals('mnop', $s->getAsciiAlpha());
  }

  public function testGetAsciiAlphaNum() {
    $s = new Scanner(new InputStream("abcdef1ghpo#mn94op"));

    $this->assertEquals('abcdef1ghpo', $s->getAsciiAlphaNum());

    // Move past the # to scan the next group of text.
    $s->next();
    $this->assertEquals('mn94op', $s->getAsciiAlphaNum());
  }

  public function testGetNumeric() {
    $s = new Scanner(new InputStream("1784a 45 9867 #"));

    $this->assertEquals('1784', $s->getNumeric());

    // Move past the 'a ' to scan the next group of text.
    $s->next();
    $s->next();
    $this->assertEquals('45', $s->getNumeric());
  }
}