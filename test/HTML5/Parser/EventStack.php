<?php
namespace Masterminds\HTML5\Tests\Parser;

use Masterminds\HTML5\Elements;
use Masterminds\HTML5\Parser\EventHandler;

/**
 * This testing class gathers events from a parser and builds a stack of events.
 * It is useful for checking the output of a tokenizer.
 *
 * IMPORTANT:
 *
 * The startTag event also kicks the parser into TEXT_RAW when it encounters
 * script or pre tags. This is to match the behavior required by the HTML5 spec,
 * which says that the tree builder must tell the tokenizer when to switch states.
 */
class EventStack implements EventHandler
{
    /**
     * @var array
     */
    protected $stack;

    public function __construct()
    {
        $this->stack = array();
    }

    /**
     * Get the event stack.
     */
    public function events()
    {
        return $this->stack;
    }

    public function depth()
    {
        return count($this->stack);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function get($index)
    {
        return $this->stack[$index];
    }

    /**
     * @param $event
     * @param null $data
     */
    protected function store($event, $data = null)
    {
        $this->stack[] = array(
            'name' => $event,
            'data' => $data
        );
    }

    /**
     * @param string $name
     * @param int $type
     * @param null $id
     * @param bool $quirks
     */
    public function doctype($name, $type = 0, $id = null, $quirks = false)
    {
        $args = array(
            $name,
            $type,
            $id,
            $quirks
        );
        $this->store('doctype', $args);
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param bool $selfClosing
     *
     * @return int
     */
    public function startTag($name, $attributes = array(), $selfClosing = false)
    {
        $args = func_get_args();
        $this->store('startTag', $args);
        if ($name === 'pre' || $name === 'script') {
            return Elements::TEXT_RAW;
        }
    }

    /**
     * @param $name
     */
    public function endTag($name)
    {
        $this->store('endTag', array(
            $name
        ));
    }

    /**
     * @param $cdata
     */
    public function comment($cdata)
    {
        $this->store('comment', array(
            $cdata
        ));
    }

    /**
     * @param string $data
     */
    public function cdata($data)
    {
        $this->store('cdata', func_get_args());
    }

    /**
     * @param $cdata
     */
    public function text($cdata)
    {
        // fprintf(STDOUT, "Received TEXT event with: " . $cdata);
        $this->store('text', array(
            $cdata
        ));
    }

    public function eof()
    {
        $this->store('eof');
    }

    /**
     * @param $msg
     * @param $line
     * @param $col
     */
    public function parseError($msg, $line, $col)
    {
        // throw new EventStackParseError(sprintf("%s (line %d, col %d)", $msg, $line, $col));
        // $this->store(sprintf("%s (line %d, col %d)", $msg, $line, $col));
        $this->store('error', func_get_args());
    }

    /**
     * @param string $name
     * @param null $data
     */
    public function processingInstruction($name, $data = null)
    {
        $this->store('pi', func_get_args());
    }
}
