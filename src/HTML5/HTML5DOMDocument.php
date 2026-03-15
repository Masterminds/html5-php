<?php

namespace Masterminds\HTML5;

use Masterminds\HTML5\Serializer\OutputRules;
use Masterminds\HTML5\Serializer\Traverser;

/**
 * Shared serializer logic for template-aware DOMDocument implementations.
 */
class HTML5DOMDocumentBase extends \DOMDocument
{
    /**
     * @var \SplObjectStorage|null
     */
    protected $templateContents;

    /**
     * @param bool $create
     *
     * @return \SplObjectStorage|null
     */
    public function html5PhpTemplateContentsStorage($create = true)
    {
        if (null === $this->templateContents && $create) {
            $this->templateContents = new \SplObjectStorage();
        }

        return $this->templateContents;
    }

    protected function html5PhpSaveHTML($node = null)
    {
        $target = null === $node ? $this : $node;
        if (!$this->containsDetachedTemplateContents($target)) {
            return parent::saveHTML($node);
        }

        $stream = fopen('php://temp', 'wb');
        $rules = new OutputRules($stream);
        $traverser = new Traverser($target, $stream, $rules);
        $traverser->walk();
        $rules->unsetTraverser();

        $html = stream_get_contents($stream, -1, 0);
        fclose($stream);

        return $html;
    }

    protected function html5PhpImportNode($node, $deep = false)
    {
        $imported = parent::importNode($node, $deep);
        TemplateContents::copySubtree($node, $imported, $deep);

        return $imported;
    }

    protected function html5PhpCloneNode($deep = false)
    {
        $class = get_class($this);
        $clone = new $class($this->xmlVersion, $this->encoding);
        TemplateContents::registerNodeClasses($clone);

        $clone->encoding = $this->encoding;
        $clone->formatOutput = $this->formatOutput;
        $clone->preserveWhiteSpace = $this->preserveWhiteSpace;
        $clone->recover = $this->recover;
        $clone->resolveExternals = $this->resolveExternals;
        $clone->strictErrorChecking = $this->strictErrorChecking;
        $clone->substituteEntities = $this->substituteEntities;
        $clone->validateOnParse = $this->validateOnParse;

        if (!$deep) {
            return $clone;
        }

        foreach ($this->childNodes as $child) {
            $clone->appendChild($clone->importNode($child, true));
        }

        return $clone;
    }

    /**
     * @param \DOMNode $node
     *
     * @return bool
     */
    protected function containsDetachedTemplateContents(\DOMNode $node)
    {
        if ($node instanceof \DOMElement && 'template' === strtolower($node->tagName)) {
            return null !== TemplateContents::find($node);
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($this->containsDetachedTemplateContents($child)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (PHP_VERSION_ID >= 80100) {
    require __DIR__ . '/HTML5DOMDocument81.php';
} else {
    class HTML5DOMDocument extends HTML5DOMDocumentBase
    {
        public function saveHTML($node = null)
        {
            return $this->html5PhpSaveHTML($node);
        }

        public function importNode($node, $deep = false)
        {
            return $this->html5PhpImportNode($node, $deep);
        }

        public function cloneNode($deep = false)
        {
            return $this->html5PhpCloneNode($deep);
        }
    }
}
