<?php

namespace Masterminds\HTML5;

class HTML5DOMElement extends HTML5DOMElementBase
{
    #[\ReturnTypeWillChange]
    public function cloneNode($deep = false)
    {
        return $this->html5PhpCloneNode($deep);
    }

    #[\ReturnTypeWillChange]
    public function appendChild($node)
    {
        return $this->html5PhpAppendChild($node);
    }

    #[\ReturnTypeWillChange]
    public function insertBefore($newnode, $refnode = null)
    {
        return $this->html5PhpInsertBefore($newnode, $refnode);
    }

    #[\ReturnTypeWillChange]
    public function replaceChild($newnode, $oldnode)
    {
        return $this->html5PhpReplaceChild($newnode, $oldnode);
    }

    #[\ReturnTypeWillChange]
    public function removeChild($oldnode)
    {
        return $this->html5PhpRemoveChild($oldnode);
    }
}
