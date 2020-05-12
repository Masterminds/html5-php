<?php

namespace Masterminds\HTML5\Parser;

/**
 * Normalizes HTML.
 *
 * This class adds missing root elements, namely <html>, <head>, <body>. <!DOCTYPE> can optionally be added
 * if specified in the tree structure - by default this is disabled.
 *
 * This library treats input HTML as a document fragment rather than a complete document (even if it has a DOCTYPE).
 * DOMDocument automatically adds missing root elements so this class aims to replicate that functionality.
 *
 * @author Kieran Brahney <kieran@supportpal.com>
 * @see    https://github.com/Masterminds/html5-php/issues/166
 */
class Normalizer
{
    /**
     * Structure of a basic HTML document.
     *
     * @var array
     */
    protected $tree = array(
        'doctype' => '',
        'html' => array(
            'start' => '<html>',
            'end' => '</html>',
            'content' => array(),
        ),
        'head' => array(
            'start' => '<head>',
            'end' => '</head>',
            'content' => array(),
        ),
        'body' => array(
            'start' => '<body>',
            'end' => '</body>',
            'content' => array(),
        ),
    );

    /**
     * What root element did we last add to.
     *
     * @var string|null
     */
    protected $previousKey = null;

    /**
     * Parse a HTML document.
     *
     * @param  string $html
     * @return void
     */
    public function loadHtml($html)
    {
        $i = 0;
        $len = \strlen($html);
        while ($i < $len) {
            if ($html[$i] === '<') {
                // Found a tag, get chars until the end of the tag.
                $tag = '';
                while ($i < $len && $html[$i] !== '>') {
                    $tag .= $html[$i++];
                }

                if ($i < $len && (string) $html[$i] === '>') {
                    $tag .= $html[$i++];

                    // Copy any whitespace following the tag.
                    // Anything added here needs to be added to the rtrim in the nodeName function.
                    while ($i < $len && \preg_match('/\s/', (string) $html[$i])) {
                        $tag .= $html[$i++];
                    }
                } else {
                    // Missing closing tag?
                    $tag .= '>';
                }

                $this->addToTree($tag);
            } else {
                $this->addToTree($html[$i++]);
            }
        }
    }

    /**
     * Format the document in a structured way (ensures root elements exists and moves scripts/css into <body>).
     *
     * @return string
     */
    public function saveHtml()
    {
        // Initialise buffer.
        $buffer = '';

        // Add <!DOCTYPE> - this is optional.
        $buffer .= $this->tree['doctype'];

        // Add <html>
        $buffer .= $this->tree['html']['start'];

        // Add head
        $buffer .= $this->tree['head']['start'];
        foreach ($this->tree['head']['content'] as $node) {
            $buffer .= $node;
        }
        $buffer .= $this->tree['head']['end'];

        // Add body
        $buffer .= $this->tree['body']['start'];
        foreach ($this->tree['body']['content'] as $node) {
            $buffer .= $node;
        }
        $buffer .= $this->tree['body']['end'];

        // Close </html> tag
        return $buffer . $this->tree['html']['end'];
    }

    /**
     * Add a node into the tree for the correct parent.
     *
     * @param string $node
     * @return void
     */
    protected function addToTree($node)
    {
        if ($node[0] == '<') {
            switch (\strtolower($this->nodeName($node))) {
                case '!doctype':
                    if (empty($this->tree['doctype'])) {
                        $this->tree['doctype'] = $node;

                        return;
                    }

                    // Don't overwrite if we've already got a doctype definition.
                    return;

                case 'html':
                    $this->addTo('html', $node, false);

                    return;

                case 'head':
                    $this->addTo('head', $node, true);

                    return;

                default:
                    $this->addTo(isset($this->previousKey) ? $this->previousKey : 'body', $node, true);

                    return;
            }
        }

        // text node
        $this->addTo(isset($this->previousKey) ? $this->previousKey : 'body', $node, true);
    }

    /**
     * Add a node to the the tree.
     *
     * @param  string $key
     * @param  string $node
     * @param  bool   $setPrevious
     * @return void
     */
    protected function addTo($key, $node, $setPrevious)
    {
        $previousKey = $key;

        if (\stripos($node, '<' . $key) !== false) {
            $this->tree[$key]['start'] = $node;
        } elseif (\stristr($node, '/' . $key . '>')) {
            $this->tree[$key]['end'] = $node;
            $previousKey = null;
        } else {
            $this->tree[$key]['content'][] = $node;
        }

        if ($setPrevious) {
            $this->previousKey = $previousKey;
        }
    }

    /**
     * Get the name of a node without </>
     *
     * @param string $node
     * @return string
     */
    protected function nodeName($node)
    {
        $name = \preg_replace('/>\s*/', '', \ltrim($node, '</'));

        $chunks = \explode(' ', $name);

        return $chunks[0];
    }
}
