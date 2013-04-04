<?php
namespace HTML5;

/**
 * Outwards facing API for HTML5.
 */
class Parser
{
    /**
     * Parses a full HTML document.
     * @param $text HTML text to parse
     * @param $builder Custom builder implementation
     * @return Parsed HTML as DOMDocument
     */
    public static function parse($text, $builder = null) {
        $tokenizer = new Tokenizer($text, $builder);
        $tokenizer->parse();
        return $tokenizer->save();
    }
    /**
     * Parses an HTML fragment.
     * @param $text HTML text to parse
     * @param $context String name of context element to pretend parsing is in.
     * @param $builder Custom builder implementation
     * @return Parsed HTML as DOMDocument
     */
    public static function parseFragment($text, $context = null, $builder = null) {
        $tokenizer = new Tokenizer($text, $builder);
        $tokenizer->parseFragment($context);
        return $tokenizer->save();
    }

}
