<?php
namespace HTML5\Tests\Parser;

class InstructionProcessorMock implements \HTML5\InstructionProcessor {

    public $name = NULL;
    public $data = NULL;
    public $count = 0;

    public function process(\DOMElement $element, $name, $data) {
        $this->name = $name;
        $this->data = $data;
        $this->count++;

        $div = $element->ownerDocument->createElement("div");
        $div->nodeValue = 'foo';

        $element->appendChild($div);

        return $div;
    }
}