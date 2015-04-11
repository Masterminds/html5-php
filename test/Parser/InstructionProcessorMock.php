<?php
namespace Masterminds\Html5\Tests\Parser;

class InstructionProcessorMock implements \Masterminds\Html5\InstructionProcessor
{

    public $name = null;

    public $data = null;

    public $count = 0;

    public function process(\DOMElement $element, $name, $data)
    {
        $this->name = $name;
        $this->data = $data;
        $this->count ++;

        $div = $element->ownerDocument->createElement("div");
        $div->nodeValue = 'foo';

        $element->appendChild($div);

        return $div;
    }
}
