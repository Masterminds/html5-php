<?php

namespace Masterminds\HTML5;

class HTML5DOMDocument extends HTML5DOMDocumentBase
{
    #[\ReturnTypeWillChange]
    public function saveHTML($node = null)
    {
        return $this->html5PhpSaveHTML($node);
    }

    #[\ReturnTypeWillChange]
    public function importNode($node, $deep = false)
    {
        return $this->html5PhpImportNode($node, $deep);
    }

    #[\ReturnTypeWillChange]
    public function cloneNode($deep = false)
    {
        return $this->html5PhpCloneNode($deep);
    }
}
