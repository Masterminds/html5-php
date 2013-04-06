# HTML5-PHP

This is a **highly experimental** fork of the html5lib PHP parser.

The need for an HTML5 parser in PHP is clear. This project extends on
the work of a previous (but seemingly abandoned) PHP parser. Beginning
with the [original source](https://code.google.com/p/html5lib/source/checkout), we have
create a newer version and are working to add the following features:

- An HTML5 serializer [in progress; early alpha]
- Support for PHP namespace [done]
- Composer support [in progress]
- Interoperability with QueryPath [not started]
- Add non-HTML namespace support to parser.

## Usage

This is how you use the `HTML5` library:

```php
<?php
// Assuming you installed from Composer:
require "vendor/autoload.php";


// An example HTML document:
$html = <<< 'HERE'
  <html>
  <head>
    <title>TEST</title>
  </head>
  <body id='foo'>
    <h1>Hello World</h1>
    <p>This is a test of the HTML5 parser.</p>
  </body>
  </html>
HERE;

// Parse the document. $dom is a DOMDocument.
$dom = HTML5::parse($html);


// Render it as HTML5:
print HTML5::saveHTML($dom);

// Or save it to a file:
HTML5::save('out.html');

?>
```

The `$dom` created by the parser is a full `DOMDocument` object. And the
`save()` and `saveHTML()` methods will take any DOMDocument.

## Notes on Serialized Formats

The serializer (`save()`, `saveHTML()`) follows the 
[section 8.9 of the HTML 5.0 spec] (http://www.w3.org/TR/2012/CR-html5-20121217/syntax.html#serializing-html-fragments).
So tags are serialized according to these rules:

- A tag with children: &lt;foo&gt;CHILDREN&lt;/foo&gt;
- A tag that cannot have content: &lt;foo&gt; (no closing tag)
- A tag that could have content, but doesn't: &lt;foo&gt;&lt;/foo&gt;

## Thanks to...

We owe a huge debt of gratitude to the original authors of html5lib.

## License

This software is released under the MIT license.
