<?php

namespace Masterminds\HTML5;

/**
 * Keep the template-aware owner document alive for detached fragments.
 */
class HTML5DOMDocumentFragmentBase extends \DOMDocumentFragment
{
    /**
     * @var \DOMDocument|null
     */
    protected $templateOwnerDocument;

    /**
     * @param \DOMDocument $document
     */
    public function html5PhpKeepTemplateOwnerDocument(\DOMDocument $document)
    {
        $this->templateOwnerDocument = $document;
    }

    protected function html5PhpCloneNode($deep = false)
    {
        $clone = parent::cloneNode($deep);

        if ($this->templateOwnerDocument instanceof \DOMDocument && method_exists($clone, 'html5PhpKeepTemplateOwnerDocument')) {
            $clone->html5PhpKeepTemplateOwnerDocument($this->templateOwnerDocument);
        }

        TemplateContents::copySubtree($this, $clone, $deep);

        return $clone;
    }
}

if (PHP_VERSION_ID >= 80100) {
    require __DIR__ . '/HTML5DOMDocumentFragment81.php';
} else {
    class HTML5DOMDocumentFragment extends HTML5DOMDocumentFragmentBase
    {
        public function cloneNode($deep = false)
        {
            return $this->html5PhpCloneNode($deep);
        }
    }
}
