<?php

namespace Masterminds\HTML5;

class HTML5DOMDocumentFragment extends HTML5DOMDocumentFragmentBase
{
    #[\ReturnTypeWillChange]
    public function cloneNode($deep = false)
    {
        return $this->html5PhpCloneNode($deep);
    }
}
