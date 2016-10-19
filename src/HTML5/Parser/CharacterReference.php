<?php
namespace Masterminds\HTML5\Parser;

use Masterminds\HTML5\Entities;
use voku\helper\UTF8;

/**
 * Manage entity references.
 *
 * This is a simple resolver for HTML5 character reference entitites.
 * See \Masterminds\HTML5\Entities for the list of supported entities.
 */
class CharacterReference
{
    /**
     * Given a name (e.g. 'amp'), lookup the UTF-8 character ('&')
     *
     * @param string $name <p>The name to look up.</p>
     * @return string <p>The character sequence. In UTF-8 this may be more than one byte.</p>
     */
    public static function lookupName($name)
    {
        // init
        $name = (string)$name;

        if (!isset($name[0])) {
            return null;
        }

        // good performance
        if (isset(Entities::$byName[$name])) {
            return Entities::$byName[$name];
        }

        // fallback
        $tmpName = UTF8::html_entity_decode('&' . $name . ';');
        if ($tmpName !== '&' . $name . ';') {
            return $tmpName;
        }

        return null;
    }

    /**
     * Given a decimal number, return the UTF-8 character.
     */
    public static function lookupDecimal($int)
    {
        return UTF8::decimal_to_chr($int);
    }

    /**
     * Given a hexidecimal number, return the UTF-8 character.
     */
    public static function lookupHex($hexdec)
    {
        return UTF8::hex_to_chr($hexdec);
    }
}
