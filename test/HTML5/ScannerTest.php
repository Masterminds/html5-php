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
}