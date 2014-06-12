<?php
namespace Masterminds\HTML5;

use Masterminds\HTML5\Parser\FileInputStream;
use Masterminds\HTML5\Parser\StringInputStream;
use Masterminds\HTML5\Parser\DOMTreeBuilder;
use Masterminds\HTML5\Parser\Scanner;
use Masterminds\HTML5\Parser\Tokenizer;
use Masterminds\HTML5\Serializer\OutputRules;
use Masterminds\HTML5\Serializer\Traverser;

/**
 * This class offers convenience methods for parsing and serializing HTML5.
 * It is roughly designed to mirror the \DOMDocument class that is
 * provided with most versions of PHP.
 *
 * EXPERIMENTAL. This may change or be completely replaced.
 */
class Dumper
{

    /**
     * Global options for the parser and serializer.
     *
     * @var array
     */
    private $options = array(
        // If the serializer should encode all entities.
        'encode_entities' => FALSE
    );

    private $errors = array();

    public function __construct(array $options = array())
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get the default options.
     *
     * @return array The default options.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Save a DOM into a given file as HTML5.
     *
     * @param mixed $dom
     *            The DOM to be serialized.
     * @param string $file
     *            The filename to be written.
     * @param array $options
     *            Configuration options when serializing the DOM. These include:
     *            - encode_entities: Text written to the output is escaped by default and not all
     *            entities are encoded. If this is set to TRUE all entities will be encoded.
     *            Defaults to FALSE.
     */
    public function save($dom, $file, $options = array())
    {
        $close = TRUE;
        if (is_resource($file)) {
            $stream = $file;
            $close = FALSE;
        } else {
            $stream = fopen($file, 'w');
        }
        $options = array_merge($this->getOptions(), $options);
        $rules = new OutputRules($stream, $options);
        $trav = new Traverser($dom, $stream, $rules, $options);

        $trav->walk();

        if ($close) {
            fclose($stream);
        }
    }

    /**
     * Convert a DOM into an HTML5 string.
     *
     * @param mixed $dom
     *            The DOM to be serialized.
     * @param array $options
     *            Configuration options when serializing the DOM. These include:
     *            - encode_entities: Text written to the output is escaped by default and not all
     *            entities are encoded. If this is set to TRUE all entities will be encoded.
     *            Defaults to FALSE.
     *
     * @return string A HTML5 documented generated from the DOM.
     */
    public function saveHTML($dom, $options = array())
    {
        $stream = fopen('php://temp', 'w');
        $this->save($dom, $stream, array_merge($this->getOptions(), $options));

        return stream_get_contents($stream, - 1, 0);
    }
}
