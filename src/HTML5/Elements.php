<?php
namespace HTML5;

class Elements {

  const TEXT_RAW = 0x01;
  const TEXT_RCDATA = 0x02;
  const OMIT_START = 0x0a;
  const OMIT_END = 0x0b;

  /**
   * The HTML5 elements as defined in http://dev.w3.org/html5/markup/elements.html.
   * @var array
   */
  public static $elements = array(
    "a" => 1,
    "abbr" => 1,
    "address" => 1,
    "area" => 1,
    "article" => 1,
    "aside" => 1,
    "audio" => 1,
    "b" => 1,
    "base" => 1,
    "bdi" => 1,
    "bdo" => 1,
    "blockquote" => 1,
    "body" => 1,
    "br" => 1,
    "button" => 1,
    "canvas" => 1,
    "caption" => 1,
    "cite" => 1,
    "code" => 1,
    "col" => 1,
    "colgroup" => 1,
    "command" => 1,
    //"data" => 1,    // This is highly experimental and only part of the whatwg spec (not w3c). See https://developer.mozilla.org/en-US/docs/HTML/Element/data
    "datalist" => 1,
    "dd" => 1,
    "del" => 1,
    "details" => 1,
    "dfn" => 1,
    "dialog" => 1,
    "div" => 1,
    "dl" => 1,
    "dt" => 1,
    "em" => 1,
    "embed" => 1,
    "fieldset" => 1,
    "figcaption" => 1,
    "figure" => 1,
    "footer" => 1,
    "form" => 1,
    "h1" => 1,
    "h2" => 1,
    "h3" => 1,
    "h4" => 1,
    "h5" => 1,
    "h6" => 1,
    "head" => 1,
    "header" => 1,
    "hgroup" => 1,
    "hr" => 1,
    "html" => 1,
    "i" => 1,
    "iframe" => 1,
    "img" => 1,
    "input" => 1,
    "kbd" => 1,
    "ins" => 1,
    "keygen" => 1,
    "label" => 1,
    "legend" => 1,
    "li" => 1,
    "link" => 1,
    "map" => 1,
    "mark" => 1,
    "menu" => 1,
    "meta" => 1,
    "meter" => 1,
    "nav" => 1,
    "noscript" => 1,
    "object" => 1,
    "ol" => 1,
    "optgroup" => 1,
    "option" => 1,
    "output" => 1,
    "p" => 1,
    "param" => 1,
    "pre" => 1,
    "progress" => 1,
    "q" => 1,
    "rp" => 1,
    "rt" => 1,
    "ruby" => 1,
    "s" => 1,
    "samp" => 1,
    "script" => 1,
    "section" => 1,
    "select" => 1,
    "small" => 1,
    "source" => 1,
    "span" => 1,
    "strong" => 1,
    "style" => 1,
    "sub" => 1,
    "summary" => 1,
    "sup" => 1,
    "table" => 1,
    "tbody" => 1,
    "td" => 1,
    "textarea" => 1,
    "tfoot" => 1,
    "th" => 1,
    "thead" => 1,
    "time" => 1,
    "title" => 1,
    "tr" => 1,
    "track" => 1,
    "u" => 1,
    "ul" => 1,
    "var" => 1,
    "video" => 1,
    "wbr" => 1,
  );

  /**
   * Test if an element is a valid html5 element.
   *
   * @param string $name
   *   The name of the element.
   *
   * @return bool
   *   True if a html5 element and false otherwise.
   */
  public static function isHtml5Element($name) {

    // html5 element names are case insensetitive. Forcing lowercase for the check.
    // Do we need this check or will all data passed here already be lowercase?
    return isset(self::$elements[strtolower($name)]);
  }
}
