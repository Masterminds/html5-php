<?php
namespace Masterminds\HTML5\Tests;

use Masterminds\Html5;

class TestCase extends \PHPUnit_Framework_TestCase
{

    const DOC_OPEN = '<!DOCTYPE html><html><head><title>test</title></head><body>';

    const DOC_CLOSE = '</body></html>';

    public function testFoo()
    {
        // Placeholder. Why is PHPUnit emitting warnings about no tests?
    }

    public function getInstance(array $options = array())
    {
        return new Html5($options);
    }

    protected function wrap($fragment)
    {
        return self::DOC_OPEN . $fragment . self::DOC_CLOSE;
    }
}
