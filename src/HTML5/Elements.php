<?php
/**
 * Provide general element functions.
 */
namespace Masterminds\HTML5;

/**
 * This class provides general information about HTML5 elements,
 * including syntactic and semantic issues.
 * Parsers and serializers can
 * use this class as a reference point for information about the rules
 * of various HTML5 elements.
 *
 * @todo consider using a bitmask table lookup. There is enough overlap in
 *       naming that this could significantly shrink the size and maybe make it
 *       faster. See the Go teams implementation at https://code.google.com/p/go/source/browse/html/atom.
 */
class Elements
{

    /**
     * Indicates an element is described in the specification.
     */
    const KNOWN_ELEMENT = 1;

    // From section 8.1.2: "script", "style"
    // From 8.2.5.4.7 ("in body" insertion mode): "noembed"
    // From 8.4 "style", "xmp", "iframe", "noembed", "noframes"
    /**
     * Indicates the contained text should be processed as raw text.
     */
    const TEXT_RAW = 2;

    // From section 8.1.2: "textarea", "title"
    /**
     * Indicates the contained text should be processed as RCDATA.
     */
    const TEXT_RCDATA = 4;

    /**
     * Indicates the tag cannot have content.
     */
    const VOID_TAG = 8;

    // "address", "article", "aside", "blockquote", "center", "details", "dialog", "dir", "div", "dl",
    // "fieldset", "figcaption", "figure", "footer", "header", "hgroup", "menu",
    // "nav", "ol", "p", "section", "summary", "ul"
    // "h1", "h2", "h3", "h4", "h5", "h6"
    // "pre", "listing"
    // "form"
    // "plaintext"
    /**
     * Indicates that if a previous event is for a P tag, that element
     * should be considered closed.
     */
    const AUTOCLOSE_P = 16;

    /**
     * Indicates that the text inside is plaintext (pre).
     */
    const TEXT_PLAINTEXT = 32;

    // See https://developer.mozilla.org/en-US/docs/HTML/Block-level_elements
    /**
     * Indicates that the tag is a block.
     */
    const BLOCK_TAG = 64;

    /**
     * Indicates that the tag allows only inline elements as child nodes.
     */
    const BLOCK_ONLY_INLINE = 128;

    /**
     * The HTML5 elements as defined in http://dev.w3.org/html5/markup/elements.html.
     *
     * @var array
     */
    public static $html5 = array(
        "a" => self::KNOWN_ELEMENT,
        "abbr" => self::KNOWN_ELEMENT,
        "address" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "area" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "article" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "aside" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "audio" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "b" => self::KNOWN_ELEMENT,
        "base" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "bdi" => self::KNOWN_ELEMENT,
        "bdo" => self::KNOWN_ELEMENT,
        "blockquote" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "body" => self::KNOWN_ELEMENT,
        "br" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "button" => self::KNOWN_ELEMENT,
        "canvas" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "caption" => self::KNOWN_ELEMENT,
        "cite" => self::KNOWN_ELEMENT,
        "code" => self::KNOWN_ELEMENT,
        "col" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "colgroup" => self::KNOWN_ELEMENT,
        "command" => self::KNOWN_ELEMENT + self::VOID_TAG,
        // "data" => self::KNOWN_ELEMENT, // This is highly experimental and only part of the whatwg spec (not w3c). See https://developer.mozilla.org/en-US/docs/HTML/Element/data
        "datalist" => self::KNOWN_ELEMENT,
        "dd" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "del" => self::KNOWN_ELEMENT,
        "details" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P,
        "dfn" => self::KNOWN_ELEMENT,
        "dialog" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P,
        "div" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "dl" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "dt" => self::KNOWN_ELEMENT,
        "em" => self::KNOWN_ELEMENT,
        "embed" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "fieldset" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "figcaption" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "figure" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "footer" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "form" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "h1" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "h2" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "h3" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "h4" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "h5" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "h6" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "head" => self::KNOWN_ELEMENT,
        "header" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "hgroup" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "hr" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "html" => self::KNOWN_ELEMENT,
        "i" => self::KNOWN_ELEMENT,
        "iframe" => self::KNOWN_ELEMENT + self::TEXT_RAW,
        "img" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "input" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "kbd" => self::KNOWN_ELEMENT,
        "ins" => self::KNOWN_ELEMENT,
        "keygen" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "label" => self::KNOWN_ELEMENT,
        "legend" => self::KNOWN_ELEMENT,
        "li" => self::KNOWN_ELEMENT,
        "link" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "map" => self::KNOWN_ELEMENT,
        "mark" => self::KNOWN_ELEMENT,
        "menu" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P,
        "meta" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "meter" => self::KNOWN_ELEMENT,
        "nav" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P,
        "noscript" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "object" => self::KNOWN_ELEMENT,
        "ol" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "optgroup" => self::KNOWN_ELEMENT,
        "option" => self::KNOWN_ELEMENT,
        "output" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "p" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + + self::BLOCK_TAG + self::BLOCK_ONLY_INLINE,
        "param" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "pre" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "progress" => self::KNOWN_ELEMENT,
        "q" => self::KNOWN_ELEMENT,
        "rp" => self::KNOWN_ELEMENT,
        "rt" => self::KNOWN_ELEMENT,
        "ruby" => self::KNOWN_ELEMENT,
        "s" => self::KNOWN_ELEMENT,
        "samp" => self::KNOWN_ELEMENT,
        "script" => self::KNOWN_ELEMENT + self::TEXT_RAW,
        "section" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "select" => self::KNOWN_ELEMENT,
        "small" => self::KNOWN_ELEMENT,
        "source" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "span" => self::KNOWN_ELEMENT,
        "strong" => self::KNOWN_ELEMENT,
        "style" => self::KNOWN_ELEMENT + self::TEXT_RAW,
        "sub" => self::KNOWN_ELEMENT,
        "summary" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P,
        "sup" => self::KNOWN_ELEMENT,
        "table" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "tbody" => self::KNOWN_ELEMENT,
        "td" => self::KNOWN_ELEMENT,
        "textarea" => self::KNOWN_ELEMENT + self::TEXT_RCDATA,
        "tfoot" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "th" => self::KNOWN_ELEMENT,
        "thead" => self::KNOWN_ELEMENT,
        "time" => self::KNOWN_ELEMENT,
        "title" => self::KNOWN_ELEMENT + self::TEXT_RCDATA,
        "tr" => self::KNOWN_ELEMENT,
        "track" => self::KNOWN_ELEMENT + self::VOID_TAG,
        "u" => self::KNOWN_ELEMENT,
        "ul" => self::KNOWN_ELEMENT + self::AUTOCLOSE_P + self::BLOCK_TAG,
        "var" => self::KNOWN_ELEMENT,
        "video" => self::KNOWN_ELEMENT + self::BLOCK_TAG,
        "wbr" => self::KNOWN_ELEMENT + self::VOID_TAG,

        // Legacy?
        'basefont' => self::VOID_TAG,
        'bgsound' => self::VOID_TAG,
        'noframes' => self::TEXT_RAW,
        'frame' => self::KNOWN_ELEMENT + self::VOID_TAG,
        'frameset' => self::KNOWN_ELEMENT,
        'center' => self::AUTOCLOSE_P,
        'dir' => self::AUTOCLOSE_P,
        'listing' => self::AUTOCLOSE_P,
        'plaintext' => self::AUTOCLOSE_P + self::TEXT_PLAINTEXT,
        'applet' => 0,
        'marquee' => 0,
        'isindex' => self::VOID_TAG,
        'xmp' => self::AUTOCLOSE_P + self::VOID_TAG + self::TEXT_RAW,
        'noembed' => self::TEXT_RAW
    );

    /**
     * The MathML elements.
     * See http://www.w3.org/wiki/MathML/Elements.
     *
     * In our case we are only concerned with presentation MathML and not content
     * MathML. There is a nice list of this subset at https://developer.mozilla.org/en-US/docs/MathML/Element.
     *
     * @var array
     */
    public static $mathml = array(
        "maction" => self::KNOWN_ELEMENT,
        "maligngroup" => self::KNOWN_ELEMENT,
        "malignmark" => self::KNOWN_ELEMENT,
        "math" => self::KNOWN_ELEMENT,
        "menclose" => self::KNOWN_ELEMENT,
        "merror" => self::KNOWN_ELEMENT,
        "mfenced" => self::KNOWN_ELEMENT,
        "mfrac" => self::KNOWN_ELEMENT,
        "mglyph" => self::KNOWN_ELEMENT,
        "mi" => self::KNOWN_ELEMENT,
        "mlabeledtr" => self::KNOWN_ELEMENT,
        "mlongdiv" => self::KNOWN_ELEMENT,
        "mmultiscripts" => self::KNOWN_ELEMENT,
        "mn" => self::KNOWN_ELEMENT,
        "mo" => self::KNOWN_ELEMENT,
        "mover" => self::KNOWN_ELEMENT,
        "mpadded" => self::KNOWN_ELEMENT,
        "mphantom" => self::KNOWN_ELEMENT,
        "mroot" => self::KNOWN_ELEMENT,
        "mrow" => self::KNOWN_ELEMENT,
        "ms" => self::KNOWN_ELEMENT,
        "mscarries" => self::KNOWN_ELEMENT,
        "mscarry" => self::KNOWN_ELEMENT,
        "msgroup" => self::KNOWN_ELEMENT,
        "msline" => self::KNOWN_ELEMENT,
        "mspace" => self::KNOWN_ELEMENT,
        "msqrt" => self::KNOWN_ELEMENT,
        "msrow" => self::KNOWN_ELEMENT,
        "mstack" => self::KNOWN_ELEMENT,
        "mstyle" => self::KNOWN_ELEMENT,
        "msub" => self::KNOWN_ELEMENT,
        "msup" => self::KNOWN_ELEMENT,
        "msubsup" => self::KNOWN_ELEMENT,
        "mtable" => self::KNOWN_ELEMENT,
        "mtd" => self::KNOWN_ELEMENT,
        "mtext" => self::KNOWN_ELEMENT,
        "mtr" => self::KNOWN_ELEMENT,
        "munder" => self::KNOWN_ELEMENT,
        "munderover" => self::KNOWN_ELEMENT
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
        "a" => self::KNOWN_ELEMENT,
        "altGlyph" => self::KNOWN_ELEMENT,
        "altGlyphDef" => self::KNOWN_ELEMENT,
        "altGlyphItem" => self::KNOWN_ELEMENT,
        "animate" => self::KNOWN_ELEMENT,
        "animateColor" => self::KNOWN_ELEMENT,
        "animateMotion" => self::KNOWN_ELEMENT,
        "animateTransform" => self::KNOWN_ELEMENT,
        "circle" => self::KNOWN_ELEMENT,
        "clipPath" => self::KNOWN_ELEMENT,
        "color-profile" => self::KNOWN_ELEMENT,
        "cursor" => self::KNOWN_ELEMENT,
        "defs" => self::KNOWN_ELEMENT,
        "desc" => self::KNOWN_ELEMENT,
        "ellipse" => self::KNOWN_ELEMENT,
        "feBlend" => self::KNOWN_ELEMENT,
        "feColorMatrix" => self::KNOWN_ELEMENT,
        "feComponentTransfer" => self::KNOWN_ELEMENT,
        "feComposite" => self::KNOWN_ELEMENT,
        "feConvolveMatrix" => self::KNOWN_ELEMENT,
        "feDiffuseLighting" => self::KNOWN_ELEMENT,
        "feDisplacementMap" => self::KNOWN_ELEMENT,
        "feDistantLight" => self::KNOWN_ELEMENT,
        "feFlood" => self::KNOWN_ELEMENT,
        "feFuncA" => self::KNOWN_ELEMENT,
        "feFuncB" => self::KNOWN_ELEMENT,
        "feFuncG" => self::KNOWN_ELEMENT,
        "feFuncR" => self::KNOWN_ELEMENT,
        "feGaussianBlur" => self::KNOWN_ELEMENT,
        "feImage" => self::KNOWN_ELEMENT,
        "feMerge" => self::KNOWN_ELEMENT,
        "feMergeNode" => self::KNOWN_ELEMENT,
        "feMorphology" => self::KNOWN_ELEMENT,
        "feOffset" => self::KNOWN_ELEMENT,
        "fePointLight" => self::KNOWN_ELEMENT,
        "feSpecularLighting" => self::KNOWN_ELEMENT,
        "feSpotLight" => self::KNOWN_ELEMENT,
        "feTile" => self::KNOWN_ELEMENT,
        "feTurbulence" => self::KNOWN_ELEMENT,
        "filter" => self::KNOWN_ELEMENT,
        "font" => self::KNOWN_ELEMENT,
        "font-face" => self::KNOWN_ELEMENT,
        "font-face-format" => self::KNOWN_ELEMENT,
        "font-face-name" => self::KNOWN_ELEMENT,
        "font-face-src" => self::KNOWN_ELEMENT,
        "font-face-uri" => self::KNOWN_ELEMENT,
        "foreignObject" => self::KNOWN_ELEMENT,
        "g" => self::KNOWN_ELEMENT,
        "glyph" => self::KNOWN_ELEMENT,
        "glyphRef" => self::KNOWN_ELEMENT,
        "hkern" => self::KNOWN_ELEMENT,
        "image" => self::KNOWN_ELEMENT,
        "line" => self::KNOWN_ELEMENT,
        "linearGradient" => self::KNOWN_ELEMENT,
        "marker" => self::KNOWN_ELEMENT,
        "mask" => self::KNOWN_ELEMENT,
        "metadata" => self::KNOWN_ELEMENT,
        "missing-glyph" => self::KNOWN_ELEMENT,
        "mpath" => self::KNOWN_ELEMENT,
        "path" => self::KNOWN_ELEMENT,
        "pattern" => self::KNOWN_ELEMENT,
        "polygon" => self::KNOWN_ELEMENT,
        "polyline" => self::KNOWN_ELEMENT,
        "radialGradient" => self::KNOWN_ELEMENT,
        "rect" => self::KNOWN_ELEMENT,
        "script" => self::KNOWN_ELEMENT + self::TEXT_RAW,
        "set" => self::KNOWN_ELEMENT,
        "stop" => self::KNOWN_ELEMENT,
        "style" => self::KNOWN_ELEMENT + self::TEXT_RAW,
        "svg" => self::KNOWN_ELEMENT,
        "switch" => self::KNOWN_ELEMENT,
        "symbol" => self::KNOWN_ELEMENT,
        "text" => self::KNOWN_ELEMENT,
        "textPath" => self::KNOWN_ELEMENT,
        "title" => self::KNOWN_ELEMENT,
        "tref" => self::KNOWN_ELEMENT,
        "tspan" => self::KNOWN_ELEMENT,
        "use" => self::KNOWN_ELEMENT,
        "view" => self::KNOWN_ELEMENT,
        "vkern" => self::KNOWN_ELEMENT
    );

    /**
     * Some attributes in SVG are case sensetitive.
     *
     * This map contains key/value pairs with the key as the lowercase attribute
     * name and the value with the correct casing.
     */
    public static $svgCaseSensitiveAttributeMap = array(
        'attributename' => 'attributeName',
        'attributetype' => 'attributeType',
        'basefrequency' => 'baseFrequency',
        'baseprofile' => 'baseProfile',
        'calcmode' => 'calcMode',
        'clippathunits' => 'clipPathUnits',
        'contentscripttype' => 'contentScriptType',
        'contentstyletype' => 'contentStyleType',
        'diffuseconstant' => 'diffuseConstant',
        'edgemode' => 'edgeMode',
        'externalresourcesrequired' => 'externalResourcesRequired',
        'filterres' => 'filterRes',
        'filterunits' => 'filterUnits',
        'glyphref' => 'glyphRef',
        'gradienttransform' => 'gradientTransform',
        'gradientunits' => 'gradientUnits',
        'kernelmatrix' => 'kernelMatrix',
        'kernelunitlength' => 'kernelUnitLength',
        'keypoints' => 'keyPoints',
        'keysplines' => 'keySplines',
        'keytimes' => 'keyTimes',
        'lengthadjust' => 'lengthAdjust',
        'limitingconeangle' => 'limitingConeAngle',
        'markerheight' => 'markerHeight',
        'markerunits' => 'markerUnits',
        'markerwidth' => 'markerWidth',
        'maskcontentunits' => 'maskContentUnits',
        'maskunits' => 'maskUnits',
        'numoctaves' => 'numOctaves',
        'pathlength' => 'pathLength',
        'patterncontentunits' => 'patternContentUnits',
        'patterntransform' => 'patternTransform',
        'patternunits' => 'patternUnits',
        'pointsatx' => 'pointsAtX',
        'pointsaty' => 'pointsAtY',
        'pointsatz' => 'pointsAtZ',
        'preservealpha' => 'preserveAlpha',
        'preserveaspectratio' => 'preserveAspectRatio',
        'primitiveunits' => 'primitiveUnits',
        'refx' => 'refX',
        'refy' => 'refY',
        'repeatcount' => 'repeatCount',
        'repeatdur' => 'repeatDur',
        'requiredextensions' => 'requiredExtensions',
        'requiredfeatures' => 'requiredFeatures',
        'specularconstant' => 'specularConstant',
        'specularexponent' => 'specularExponent',
        'spreadmethod' => 'spreadMethod',
        'startoffset' => 'startOffset',
        'stddeviation' => 'stdDeviation',
        'stitchtiles' => 'stitchTiles',
        'surfacescale' => 'surfaceScale',
        'systemlanguage' => 'systemLanguage',
        'tablevalues' => 'tableValues',
        'targetx' => 'targetX',
        'targety' => 'targetY',
        'textlength' => 'textLength',
        'viewbox' => 'viewBox',
        'viewtarget' => 'viewTarget',
        'xchannelselector' => 'xChannelSelector',
        'ychannelselector' => 'yChannelSelector',
        'zoomandpan' => 'zoomAndPan'
    );

    /**
     * Some SVG elements are case sensetitive.
     * This map contains these.
     *
     * The map contains key/value store of the name is lowercase as the keys and
     * the correct casing as the value.
     */
    public static $svgCaseSensitiveElementMap = array(
        'altglyph' => 'altGlyph',
        'altglyphdef' => 'altGlyphDef',
        'altglyphitem' => 'altGlyphItem',
        'animatecolor' => 'animateColor',
        'animatemotion' => 'animateMotion',
        'animatetransform' => 'animateTransform',
        'clippath' => 'clipPath',
        'feblend' => 'feBlend',
        'fecolormatrix' => 'feColorMatrix',
        'fecomponenttransfer' => 'feComponentTransfer',
        'fecomposite' => 'feComposite',
        'feconvolvematrix' => 'feConvolveMatrix',
        'fediffuselighting' => 'feDiffuseLighting',
        'fedisplacementmap' => 'feDisplacementMap',
        'fedistantlight' => 'feDistantLight',
        'feflood' => 'feFlood',
        'fefunca' => 'feFuncA',
        'fefuncb' => 'feFuncB',
        'fefuncg' => 'feFuncG',
        'fefuncr' => 'feFuncR',
        'fegaussianblur' => 'feGaussianBlur',
        'feimage' => 'feImage',
        'femerge' => 'feMerge',
        'femergenode' => 'feMergeNode',
        'femorphology' => 'feMorphology',
        'feoffset' => 'feOffset',
        'fepointlight' => 'fePointLight',
        'fespecularlighting' => 'feSpecularLighting',
        'fespotlight' => 'feSpotLight',
        'fetile' => 'feTile',
        'feturbulence' => 'feTurbulence',
        'foreignobject' => 'foreignObject',
        'glyphref' => 'glyphRef',
        'lineargradient' => 'linearGradient',
        'radialgradient' => 'radialGradient',
        'textpath' => 'textPath'
    );

    /**
     * Check whether the given element meets the given criterion.
     *
     * Example:
     *
     * Elements::isA('script', Elements::TEXT_RAW); // Returns true.
     *
     * Elements::isA('script', Elements::TEXT_RCDATA); // Returns false.
     *
     * @param string $name
     *            The element name.
     * @param int $mask
     *            One of the constants on this class.
     * @return boolean true if the element matches the mask, false otherwise.
     */
    public static function isA($name, $mask)
    {
        if (! static::isElement($name)) {
            return false;
        }

        return (static::element($name) & $mask) == $mask;
    }

    /**
     * Test if an element is a valid html5 element.
     *
     * @param string $name
     *            The name of the element.
     *
     * @return bool True if a html5 element and false otherwise.
     */
    public static function isHtml5Element($name)
    {
        // html5 element names are case insensetitive. Forcing lowercase for the check.
        // Do we need this check or will all data passed here already be lowercase?
        return isset(static::$html5[strtolower($name)]);
    }

    /**
     * Test if an element name is a valid MathML presentation element.
     *
     * @param string $name
     *            The name of the element.
     *
     * @return bool True if a MathML name and false otherwise.
     */
    public static function isMathMLElement($name)
    {
        // MathML is case-sensetitive unlike html5 elements.
        return isset(static::$mathml[$name]);
    }

    /**
     * Test if an element is a valid SVG element.
     *
     * @param string $name
     *            The name of the element.
     *
     * @return boolean True if a SVG element and false otherise.
     */
    public static function isSvgElement($name)
    {
        // SVG is case-sensetitive unlike html5 elements.
        return isset(static::$svg[$name]);
    }

    /**
     * Is an element name valid in an html5 document.
     *
     * This includes html5 elements along with other allowed embedded content
     * such as svg and mathml.
     *
     * @param string $name
     *            The name of the element.
     *
     * @return bool True if valid and false otherwise.
     */
    public static function isElement($name)
    {
        return static::isHtml5Element($name) || static::isMathMLElement($name) || static::isSvgElement($name);
    }

    /**
     * Get the element mask for the given element name.
     *
     * @param string $name
     *            The name of the element.
     *
     * @return int|bool The element mask or false if element does not exist.
     */
    public static function element($name)
    {
        if (isset(static::$html5[$name])) {
            return static::$html5[$name];
        }
        if (isset(static::$svg[$name])) {
            return static::$svg[$name];
        }
        if (isset(static::$mathml[$name])) {
            return static::$mathml[$name];
        }

        return false;
    }

    /**
     * Normalize a SVG element name to its proper case and form.
     *
     * @param string $name
     *            The name of the element.
     *
     * @return string The normalized form of the element name.
     */
    public static function normalizeSvgElement($name)
    {
        $name = strtolower($name);
        if (isset(static::$svgCaseSensitiveElementMap[$name])) {
            $name = static::$svgCaseSensitiveElementMap[$name];
        }

        return $name;
    }

    /**
     * Normalize a SVG attribute name to its proper case and form.
     *
     * @param string $name
     *            The name of the attribute.
     *
     * @return string The normalized form of the attribute name.
     */
    public static function normalizeSvgAttribute($name)
    {
        $name = strtolower($name);
        if (isset(static::$svgCaseSensitiveAttributeMap[$name])) {
            $name = static::$svgCaseSensitiveAttributeMap[$name];
        }

        return $name;
    }

    /**
     * Normalize a MathML attribute name to its proper case and form.
     *
     * Note, all MathML element names are lowercase.
     *
     * @param string $name
     *            The name of the attribute.
     *
     * @return string The normalized form of the attribute name.
     */
    public static function normalizeMathMlAttribute($name)
    {
        $name = strtolower($name);

        // Only one attribute has a mixed case form for MathML.
        if ($name == 'definitionurl') {
            $name = 'definitionURL';
        }

        return $name;
    }
}
