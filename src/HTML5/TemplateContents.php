<?php

namespace Masterminds\HTML5;

/**
 * Access detached template contents stored on HTML5-aware DOM nodes.
 */
class TemplateContents
{
    /**
     * Fallback storage for caller-supplied plain DOMDocument instances on
     * runtimes without WeakMap support.
     *
     * @var array
     */
    private static $storage = array();

    /**
     * Weakly-keyed storage for caller-supplied plain DOMDocument instances.
     *
     * @var object|null
     */
    private static $weakStorage;

    /**
     * @param \DOMDocument $document
     *
     * @return bool
     */
    public static function registerNodeClasses(\DOMDocument $document)
    {
        class_exists('Masterminds\\HTML5\\HTML5DOMElement');
        class_exists('Masterminds\\HTML5\\HTML5DOMDocumentFragment');

        $elementClass = ltrim(get_class($document->createElement('html5-php-probe')), '\\');
        if (!in_array($elementClass, array('DOMElement', 'Masterminds\\HTML5\\HTML5DOMElement'), true)) {
            return false;
        }

        $fragmentClass = ltrim(get_class($document->createDocumentFragment()), '\\');
        if (!in_array($fragmentClass, array('DOMDocumentFragment', 'Masterminds\\HTML5\\HTML5DOMDocumentFragment'), true)) {
            return false;
        }

        if ('Masterminds\\HTML5\\HTML5DOMElement' !== $elementClass) {
            $document->registerNodeClass('DOMElement', 'Masterminds\\HTML5\\HTML5DOMElement');
        }

        if ('Masterminds\\HTML5\\HTML5DOMDocumentFragment' !== $fragmentClass) {
            $document->registerNodeClass('DOMDocumentFragment', 'Masterminds\\HTML5\\HTML5DOMDocumentFragment');
        }

        return true;
    }

    /**
     * @param \DOMElement          $element
     * @param \DOMDocumentFragment $fragment
     *
     * @return bool
     */
    public static function store(\DOMElement $element, \DOMDocumentFragment $fragment)
    {
        if (method_exists($element, 'html5PhpSetTemplateContents')) {
            $element->html5PhpSetTemplateContents($fragment);
        }

        $storage = self::storage($element->ownerDocument, true);
        $storage[$element] = $fragment;

        return true;
    }

    /**
     * @param \DOMElement $element
     *
     * @return \DOMDocumentFragment|null
     */
    public static function find(\DOMElement $element)
    {
        if (method_exists($element, 'html5PhpTemplateContents')) {
            $fragment = $element->html5PhpTemplateContents();
            if ($fragment instanceof \DOMDocumentFragment) {
                return $fragment;
            }
        }

        $storage = self::storage($element->ownerDocument, false);
        if (!$storage || !$storage->offsetExists($element)) {
            return null;
        }

        return $storage[$element];
    }

    /**
     * @param \DOMElement $element
     */
    public static function forget(\DOMElement $element)
    {
        if (method_exists($element, 'html5PhpSetTemplateContents')) {
            $element->html5PhpSetTemplateContents(null);
        }

        $storage = self::storage($element->ownerDocument, false);
        if ($storage && $storage->offsetExists($element)) {
            $storage->offsetUnset($element);
        }
    }

    /**
     * Copy detached template contents from one DOM subtree to another.
     *
     * @param \DOMNode $source
     * @param \DOMNode $target
     * @param bool     $deep
     *
     * @return \DOMNode
     */
    public static function copySubtree(\DOMNode $source, \DOMNode $target, $deep)
    {
        if (!$deep) {
            return $target;
        }

        if ($source instanceof \DOMElement && $target instanceof \DOMElement) {
            $contents = self::find($source);
            if ($contents instanceof \DOMDocumentFragment) {
                self::store($target, self::cloneFragment($contents, $target->ownerDocument));
            }
        }

        $sourceChildren = self::nodeListToArray($source->childNodes);
        $targetChildren = self::nodeListToArray($target->childNodes);
        $length = count($sourceChildren);
        if ($length > count($targetChildren)) {
            $length = count($targetChildren);
        }

        for ($i = 0; $i < $length; ++$i) {
            self::copySubtree($sourceChildren[$i], $targetChildren[$i], true);
        }

        return $target;
    }

    /**
     * @param \DOMDocument         $document
     * @param bool                 $create
     *
     * @return \SplObjectStorage|null
     */
    protected static function storage(\DOMDocument $document, $create)
    {
        if (method_exists($document, 'html5PhpTemplateContentsStorage')) {
            return $document->html5PhpTemplateContentsStorage($create);
        }

        if (class_exists('WeakMap')) {
            if (null === self::$weakStorage) {
                self::$weakStorage = new \WeakMap();
            }

            if (!isset(self::$weakStorage[$document])) {
                if (!$create) {
                    return null;
                }

                self::$weakStorage[$document] = new \SplObjectStorage();
            }

            return self::$weakStorage[$document];
        }

        $key = spl_object_hash($document);
        if (!isset(self::$storage[$key])) {
            if (!$create) {
                return null;
            }

            self::$storage[$key] = new \SplObjectStorage();
        }

        return self::$storage[$key];
    }

    /**
     * @param \DOMDocumentFragment $fragment
     * @param \DOMDocument         $document
     *
     * @return \DOMDocumentFragment
     */
    protected static function cloneFragment(\DOMDocumentFragment $fragment, \DOMDocument $document)
    {
        $clone = $document->createDocumentFragment();

        foreach (self::nodeListToArray($fragment->childNodes) as $child) {
            if ($document === $child->ownerDocument) {
                $copy = $child->cloneNode(true);
            } else {
                $copy = $document->importNode($child, true);
            }
            $clone->appendChild($copy);
            self::copySubtree($child, $copy, true);
        }

        return $clone;
    }

    /**
     * @param \DOMNodeList $nodes
     *
     * @return array
     */
    protected static function nodeListToArray(\DOMNodeList $nodes)
    {
        $items = array();
        foreach ($nodes as $node) {
            $items[] = $node;
        }

        return $items;
    }
}
