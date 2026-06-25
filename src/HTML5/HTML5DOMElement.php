<?php

namespace Masterminds\HTML5;

/**
 * Shared template-aware behavior for DOMElement implementations.
 */
class HTML5DOMElementBase extends \DOMElement
{
    /**
     * @var \DOMDocumentFragment|null
     */
    protected $templateContents;

    /**
     * @param \DOMDocumentFragment|null $fragment
     */
    public function html5PhpSetTemplateContents($fragment)
    {
        $this->templateContents = $fragment;
    }

    /**
     * @return \DOMDocumentFragment|null
     */
    public function html5PhpTemplateContents()
    {
        return $this->templateContents;
    }

    protected function html5PhpHasDetachedTemplateContents()
    {
        return 'template' === strtolower($this->tagName) && $this->templateContents instanceof \DOMDocumentFragment;
    }

    protected function html5PhpCloneNode($deep = false)
    {
        $clone = parent::cloneNode($deep);
        TemplateContents::copySubtree($this, $clone, $deep);

        return $clone;
    }

    protected function html5PhpAppendChild($node)
    {
        if ($this->html5PhpHasDetachedTemplateContents()) {
            return $this->templateContents->appendChild($node);
        }

        return parent::appendChild($node);
    }

    protected function html5PhpInsertBefore($newnode, $refnode = null)
    {
        if ($this->html5PhpHasDetachedTemplateContents()) {
            if (null === $refnode) {
                return $this->templateContents->appendChild($newnode);
            }

            return $this->templateContents->insertBefore($newnode, $refnode);
        }

        return parent::insertBefore($newnode, $refnode);
    }

    protected function html5PhpReplaceChild($newnode, $oldnode)
    {
        if ($this->html5PhpHasDetachedTemplateContents()) {
            return $this->templateContents->replaceChild($newnode, $oldnode);
        }

        return parent::replaceChild($newnode, $oldnode);
    }

    protected function html5PhpRemoveChild($oldnode)
    {
        if ($this->html5PhpHasDetachedTemplateContents()) {
            return $this->templateContents->removeChild($oldnode);
        }

        return parent::removeChild($oldnode);
    }
}

if (PHP_VERSION_ID >= 80100) {
    require __DIR__ . '/HTML5DOMElement81.php';
} else {
    class HTML5DOMElement extends HTML5DOMElementBase
    {
        public function cloneNode($deep = false)
        {
            return $this->html5PhpCloneNode($deep);
        }

        public function appendChild($node)
        {
            return $this->html5PhpAppendChild($node);
        }

        public function insertBefore($newnode, $refnode = null)
        {
            return $this->html5PhpInsertBefore($newnode, $refnode);
        }

        public function replaceChild($newnode, $oldnode)
        {
            return $this->html5PhpReplaceChild($newnode, $oldnode);
        }

        public function removeChild($oldnode)
        {
            return $this->html5PhpRemoveChild($oldnode);
        }
    }
}
