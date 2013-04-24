<?php
namespace HTML5\Tests;

use \HTML5\Elements;

require_once 'TestCase.php';

class ElementsTest extends TestCase {

  public function testIsHtml5Element() {
    $html5 = array(
      "a",
      "abbr",
      "address",
      "area",
      "article",
      "aside",
      "audio",
      "b",
      "base",
      "bdi",
      "bdo",
      "blockquote",
      "body",
      "br",
      "button",
      "canvas",
      "caption",
      "cite",
      "code",
      "col",
      "colgroup",
      "command",
      //"data",
      "datalist",
      "dd",
      "del",
      "details",
      "dfn",
      "dialog",
      "div",
      "dl",
      "dt",
      "em",
      "embed",
      "fieldset",
      "figcaption",
      "figure",
      "footer",
      "form",
      "h1",
      "h2",
      "h3",
      "h4",
      "h5",
      "h6",
      "head",
      "header",
      "hgroup",
      "hr",
      "html",
      "i",
      "iframe",
      "img",
      "input",
      "ins",
      "kbd",
      "keygen",
      "label",
      "legend",
      "li",
      "link",
      "map",
      "mark",
      "menu",
      "meta",
      "meter",
      "nav",
      "noscript",
      "object",
      "ol",
      "optgroup",
      "option",
      "output",
      "p",
      "param",
      "pre",
      "progress",
      "q",
      "rp",
      "rt",
      "ruby",
      "s",
      "samp",
      "script",
      "section",
      "select",
      "small",
      "source",
      "span",
      "strong",
      "style",
      "sub",
      "summary",
      "sup",
      "table",
      "tbody",
      "td",
      "textarea",
      "tfoot",
      "th",
      "thead",
      "time",
      "title",
      "tr",
      "track",
      "u",
      "ul",
      "var",
      "video",
      "wbr",
    );

    foreach ($html5 as $element) {
      $this->assertTrue(Elements::isHtml5Element($element), 'html5 element test failed on: ' . $element);

      $this->assertTrue(Elements::isHtml5Element(strtoupper($element)), 'html5 element test failed on: ' . strtoupper($element));
    }

    $nonhtml5 = array('foo', 'bar', 'baz');
    foreach ($nonhtml5 as $element) {
      $this->assertFalse(Elements::isHtml5Element($element), 'html5 element test failed on: ' . $element);

      $this->assertFalse(Elements::isHtml5Element(strtoupper($element)), 'html5 element test failed on: ' . strtoupper($element));
    }


  }

}