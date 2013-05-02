<?php
namespace HTML5;

/**
 * Provide general element functions.
 *
 * @todo consider using a bitmask table lookup. There is enought overlap in
 *   naming that this could significantly shrink the size and maybe make it
 *   faster. See the Go teams implementation at https://code.google.com/p/go/source/browse/html/atom.
 */
class Elements {

  const KNOWN_ELEMENT = 1;
  const TEXT_RAW = 2;
  const TEXT_RCDATA = 4;
  const VOID_TAG = 8;

  // "address", "article", "aside", "blockquote", "center", "details", "dialog", "dir", "div", "dl",
  // "fieldset", "figcaption", "figure", "footer", "header", "hgroup", "menu",
  // "nav", "ol", "p", "section", "summary", "ul"
  // "h1", "h2", "h3", "h4", "h5", "h6"
  //  "pre", "listing"
  //  "form"
  //  "plaintext"
  const AUTOCLOSE_P = 16;

  const TEXT_PLAINTEXT = 32;


  /**
   * The HTML5 elements as defined in http://dev.w3.org/html5/markup/elements.html.
   * @var array
   */
  public static $elements = array(
    "a" => 1,
    "abbr" => 1,
    "address" => 25, // NORMAL | VOID_TAG |  AUTOCLOSE_P
    "area" => 9,  // NORMAL | VOID_TAG
    "article" => 17, // NORMAL | AUTOCLOSE_P
    "aside" => 17, // NORMAL | AUTOCLOSE_P,
    "audio" => 1,
    "b" => 1,
    "base" => 9, // NORMAL | VOID_TAG
    "bdi" => 1,
    "bdo" => 1,
    "blockquote" => 17, // NORMAL | AUTOCLOSE_P,
    "body" => 1,
    "br" => 9, // NORMAL | VOID_TAG
    "button" => 1,
    "canvas" => 1,
    "caption" => 1,
    "cite" => 1,
    "code" => 1,
    "col" => 9,  // NORMAL | VOID_TAG
    "colgroup" => 1,
    "command" => 9, // NORMAL | VOID_TAG
    //"data" => 1,    // This is highly experimental and only part of the whatwg spec (not w3c). See https://developer.mozilla.org/en-US/docs/HTML/Element/data
    "datalist" => 1,
    "dd" => 1,
    "del" => 1,
    "details" => 17, // NORMAL | AUTOCLOSE_P,
    "dfn" => 1,
    "dialog" => 17, // NORMAL | AUTOCLOSE_P,
    "div" => 17, // NORMAL | AUTOCLOSE_P,
    "dl" => 17, // NORMAL | AUTOCLOSE_P,
    "dt" => 1,
    "em" => 1,
    "embed" => 9, // NORMAL | VOID_TAG
    "fieldset" => 17, // NORMAL | AUTOCLOSE_P,
    "figcaption" => 17, // NORMAL | AUTOCLOSE_P,
    "figure" => 17, // NORMAL | AUTOCLOSE_P,
    "footer" => 17, // NORMAL | AUTOCLOSE_P,
    "form" => 17, // NORMAL | AUTOCLOSE_P,
    "h1" => 17, // NORMAL | AUTOCLOSE_P,
    "h2" => 17, // NORMAL | AUTOCLOSE_P,
    "h3" => 17, // NORMAL | AUTOCLOSE_P,
    "h4" => 17, // NORMAL | AUTOCLOSE_P,
    "h5" => 17, // NORMAL | AUTOCLOSE_P,
    "h6" => 17, // NORMAL | AUTOCLOSE_P,
    "head" => 1,
    "header" => 17, // NORMAL | AUTOCLOSE_P,
    "hgroup" => 17, // NORMAL | AUTOCLOSE_P,
    "hr" => 9, // NORMAL | VOID_TAG
    "html" => 1,
    "i" => 1,
    "iframe" => 3, // NORMAL | TEXT_RAW
    "img" => 9, // NORMAL | VOID_TAG
    "input" => 9, // NORMAL | VOID_TAG
    "kbd" => 1,
    "ins" => 1,
    "keygen" => 9, // NORMAL | VOID_TAG
    "label" => 1,
    "legend" => 1,
    "li" => 1,
    "link" => 9, // NORMAL | VOID_TAG
    "map" => 1,
    "mark" => 1,
    "menu" => 17, // NORMAL | AUTOCLOSE_P,
    "meta" => 9, // NORMAL | VOID_TAG
    "meter" => 1,
    "nav" => 17, // NORMAL | AUTOCLOSE_P,
    "noscript" => 3, // NORMAL | TEXT_RAW
    "object" => 1,
    "ol" => 17, // NORMAL | AUTOCLOSE_P,
    "optgroup" => 1,
    "option" => 1,
    "output" => 1,
    "p" => 17, // NORMAL | AUTOCLOSE_P,
    "param" => 9, // NORMAL | VOID_TAG
    "pre" => 19, // NORMAL | TEXT_RAW | AUTOCLOSE_P
    "progress" => 1,
    "q" => 1,
    "rp" => 1,
    "rt" => 1,
    "ruby" => 1,
    "s" => 1,
    "samp" => 1,
    "script" => 3, // NORMAL | TEXT_RAW
    "section" => 17, // NORMAL | AUTOCLOSE_P,
    "select" => 1,
    "small" => 1,
    "source" => 9, // NORMAL | VOID_TAG
    "span" => 1,
    "strong" => 1,
    "style" => 1,
    "sub" => 1,
    "summary" => 17, // NORMAL | AUTOCLOSE_P,
    "sup" => 1,
    "table" => 1,
    "tbody" => 1,
    "td" => 1,
    "textarea" => 5, // NORMAL | TEXT_RCDATA
    "tfoot" => 1,
    "th" => 1,
    "thead" => 1,
    "time" => 1,
    "title" => 5, // NORMAL | TEXT_RCDATA
    "tr" => 1,
    "track" => 9, // NORMAL | VOID_TAG
    "u" => 1,
    "ul" => 17, // NORMAL | AUTOCLOSE_P,
    "var" => 1,
    "video" => 1,
    "wbr" => 9, // NORMAL | VOID_TAG

    // Legacy?
    'basefont' => 8, // VOID_TAG
    'bgsound' => 8, // VOID_TAG
    'noframes' => 2, // RAW_TEXT
    'frame' => 9,  // NORMAL | VOID_TAG
    'frameset' => 1,
    'center' => 16, 'dir' => 16, 'listing' => 16, // AUTOCLOSE_P
    'plaintext' => 48, // AUTOCLOSE_P | TEXT_PLAINTEXT
    'applet' => 0,
    'marquee' => 0,
    'isindex' => 8, // VOID_TAG
    'xmp' => 18, // AUTOCLOSE_P | VOID_TAG
    'noembed' => 2, // RAW_TEXT
  );

  /**
   * The MathML elements. See http://www.w3.org/wiki/MathML/Elements.
   *
   * In our case we are only concerned with presentation MathML and not content
   * MathML. There is a nice list of this subset at https://developer.mozilla.org/en-US/docs/MathML/Element.
   * 
   * @var array
   */
  public static $mathml = array(
    "maction" => 1,
    "maligngroup" => 1,
    "malignmark" => 1,
    "math" => 1,
    "menclose" => 1,
    "merror" => 1,
    "mfenced" => 1,
    "mfrac" => 1,
    "mglyph" => 1,
    "mi" => 1,
    "mlabeledtr" => 1,
    "mlongdiv" => 1,
    "mmultiscripts" => 1,
    "mn" => 1,
    "mo" => 1,
    "mover" => 1,
    "mpadded" => 1,
    "mphantom" => 1,
    "mroot" => 1,
    "mrow" => 1,
    "ms" => 1,
    "mscarries" => 1,
    "mscarry" => 1,
    "msgroup" => 1,
    "msline" => 1,
    "mspace" => 1,
    "msqrt" => 1,
    "msrow" => 1,
    "mstack" => 1,
    "mstyle" => 1,
    "msub" => 1,
    "msup" => 1,
    "msubsup" => 1,
    "mtable" => 1,
    "mtd" => 1,
    "mtext" => 1,
    "mtr" => 1,
    "munder" => 1,
    "munderover" => 1,
  );

  /**
   * The svg elements.
   *
   * The Mozilla documentation has a good list at https://developer.mozilla.org/en-US/docs/SVG/Element.
   * The w3c list appears to be lacking in some areas like filter effect elements.
   * That list can be found at http://www.w3.org/wiki/SVG/Elements.
   *
   * Note, FireFox appears to do a better job rendering filter effects than chrome.
   * While they are in the spec I'm not sure how widely implemented they are.
   *
   * @var array
   */
  public static $svg = array(
    "a" => 1,
    "altGlyph" => 1,
    "altGlyphDef" => 1,
    "altGlyphItem" => 1,
    "animate" => 1,
    "animateColor" => 1,
    "animateMotion" => 1,
    "animateTransform" => 1,
    "circle" => 1,
    "clipPath" => 1,
    "color-profile" => 1,
    "cursor" => 1,
    "defs" => 1,
    "desc" => 1,
    "ellipse" => 1,
    "feBlend" => 1,
    "feColorMatrix" => 1,
    "feComponentTransfer" => 1,
    "feComposite" => 1,
    "feConvolveMatrix" => 1,
    "feDiffuseLighting" => 1,
    "feDisplacementMap" => 1,
    "feDistantLight" => 1,
    "feFlood" => 1,
    "feFuncA" => 1,
    "feFuncB" => 1,
    "feFuncG" => 1,
    "feFuncR" => 1,
    "feGaussianBlur" => 1,
    "feImage" => 1,
    "feMerge" => 1,
    "feMergeNode" => 1,
    "feMorphology" => 1,
    "feOffset" => 1,
    "fePointLight" => 1,
    "feSpecularLighting" => 1,
    "feSpotLight" => 1,
    "feTile" => 1,
    "feTurbulence" => 1,
    "filter" => 1,
    "font" => 1,
    "font-face" => 1,
    "font-face-format" => 1,
    "font-face-name" => 1,
    "font-face-src" => 1,
    "font-face-uri" => 1,
    "foreignObject" => 1,
    "g" => 1,
    "glyph" => 1,
    "glyphRef" => 1,
    "hkern" => 1,
    "image" => 1,
    "line" => 1,
    "linearGradient" => 1,
    "marker" => 1,
    "mask" => 1,
    "metadata" => 1,
    "missing-glyph" => 1,
    "mpath" => 1,
    "path" => 1,
    "pattern" => 1,
    "polygon" => 1,
    "polyline" => 1,
    "radialGradient" => 1,
    "rect" => 1,
    "script" => 1,
    "set" => 1,
    "stop" => 1,
    "style" => 3, // NORMAL | RAW_TEXT
    "svg" => 1,
    "switch" => 1,
    "symbol" => 1,
    "text" => 1,
    "textPath" => 1,
    "title" => 1,
    "tref" => 1,
    "tspan" => 1,
    "use" => 1,
    "view" => 1,
    "vkern" => 1,
  );

  /**
   * Check whether the given element meets the given criterion.
   *
   * Example:
   *
   *     Elements::isA('script', Elements::TEXT_RAW); // Returns true.
   *
   *     Elements::isA('script', Elements::TEXT_RCDATA); // Returns false.
   *
   * @param string $name
   *   The element name.
   * @param int $mask
   *   One of the constants on this class.
   * @return boolean
   *   TRUE if the element matches the mask, FALSE otherwise.
   */
  public static function isA($name, $mask) {
    if (!self::isElement($name)) {
      return FALSE;
    }

    return (self::element($name) & $mask) == $mask;
  }

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

  /**
   * Test if an element name is a valid MathML presentation element.
   *
   * @param string $name
   *   The name of the element.
   *
   * @return bool
   *   True if a MathML name and false otherwise.
   */
  public static function isMathMLElement($name) {

    // MathML is case-sensetitive unlike html5 elements.
    return isset(self::$mathml[$name]);
  }

  /**
   * Test if an element is a valid SVG element.
   *
   * @param string $name
   *   The name of the element.
   *
   * @return boolean
   *   True if a SVG element and false otherise.
   */
  public static function isSvgElement($name) {

    // SVG is case-sensetitive unlike html5 elements.
    return isset(self::$svg[$name]);
  }

  /**
   * Is an element name valid in an html5 document.
   *
   * This includes html5 elements along with other allowed embedded content
   * such as svg and mathml.
   * 
   * @param string $name 
   *   The name of the element.
   *
   * @return bool
   *   True if valid and false otherwise.
   */
  public static function isElement($name) {
    return self::isHtml5Element($name) || self::isMathMLElement($name) || self::isSvgElement($name);
  }

  /**
   * Get the element mask for the given element name.
   */
  public static function element($name) {
    if (isset(self::$elements[$name])) {
      return self::$elements[$name];
    }
    if (isset(self::$svg[$name])) {
      return self::$svg[$name];
    }
    if (isset(self::$mathml[$name])) {
      return self::$mathml[$name];
    }

    return FALSE;
  }
}
