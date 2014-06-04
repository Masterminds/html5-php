# HTML5-PHP

The need for an HTML5 parser in PHP is clear. This project initially
began with the seemingly abandoned `html5lib` project [original source](https://code.google.com/p/html5lib/source/checkout).
But after some initial refactoring work, we began a new parser.

- An HTML5 serializer
- Support for PHP namespaces
- Composer support
- Event-based (SAX-like) parser
- DOM tree builder
- Interoperability with QueryPath [[in progress](https://github.com/technosophos/querypath/issues/114)]

[![Build Status](https://travis-ci.org/Masterminds/html5-php.png?branch=master)](https://travis-ci.org/Masterminds/html5-php) [![Latest Stable Version](https://poser.pugx.org/masterminds/html5/v/stable.png)](https://packagist.org/packages/masterminds/html5) [![Coverage Status](https://coveralls.io/repos/Masterminds/html5-php/badge.png?branch=master)](https://coveralls.io/r/Masterminds/html5-php?branch=master)

## Installation

Install HTML5-PHP using [composer](http://getcomposer.org/).

To install, add `masterminds/html5` to your `composer.json` file:

```
{
  "require" : {
    "masterminds/html5": "1.*"
  },
}
```

(You may substitute `1.*` for a more specific release tag, of
course.)

From there, use the `composer install` or `composer update` commands to
install.

## Basic Usage

HTML5-PHP has a high-level API and a low-level API. 

Here is how you use the high-level `HTML5` library API:

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
$html5 = new HTML5();
$dom = $html5->loadHTML($html);

// Render it as HTML5:
print $html5->saveHTML($dom);

// Or save it to a file:
$html5->save($dom, 'out.html');

?>
```

The `$dom` created by the parser is a full `DOMDocument` object. And the
`save()` and `saveHTML()` methods will take any DOMDocument.


## The Low-Level API

This library provides the following low-level APIs that you can use to
create more customized HTML5 tools:

- An `InputStream` abstraction that can work with different kinds of
input source (not just files and strings).
- A SAX-like event-based parser that you can hook into for special kinds
of parsing.
- A flexible error-reporting mechanism that can be tuned to document
syntax checking.
- A DOM implementation that uses PHP's built-in DOM library.

The unit tests exercise each piece of the API, and every public function
is well-documented.

### Parser Design

The parser is designed as follows:

- The `InputStream` portion handles direct I/O.
- The `Scanner` handles scanning on behalf of the parser.
- The `Tokenizer` requests data off of the scanner, parses it, clasifies
it, and sends it to an `EventHandler`. It is a *recursive descent parser.*
- The `EventHandler` receives notifications and data for each specific
semantic event that occurs during tokenization.
- The `DOMBuilder` is an `EventHandler` that listens for tokenizing
events and builds a document tree (`DOMDocument`) based on the events.

### Serializer Design

The serializer takes a data structure (the `DOMDocument`) and transforms
it into a character representation -- an HTML5 document.

The serializer is broken into three parts:

- The `OutputRules` contain the rules to turn DOM elements into strings. The
rules are an implementation of the interface `RulesInterface` allowing for
different rule sets to be used. 
- The `Traverser`, which is a special-purpose tree walker. It visits
each node node in the tree and uses the `OutputRules` to transform the node
into a string.
- `\HTML5` manages the `Traverser` and stores the resultant data
in the correct place.

The serializer (`save()`, `saveHTML()`) follows the 
[section 8.9 of the HTML 5.0 spec](http://www.w3.org/TR/2012/CR-html5-20121217/syntax.html#serializing-html-fragments).
So tags are serialized according to these rules:

- A tag with children: &lt;foo&gt;CHILDREN&lt;/foo&gt;
- A tag that cannot have content: &lt;foo&gt; (no closing tag)
- A tag that could have content, but doesn't: &lt;foo&gt;&lt;/foo&gt;

## Known Issues (Or, Things We Designed Against the Spec)

Please check the issue queue for a full list, but the following are
issues known issues that are not presently on the roadmap:

- Namespaces: HTML5 only [supports a selected list of namespaces](http://www.w3.org/TR/html5/infrastructure.html#namespaces)
  and they do not operate in the same way as XML namespaces. A `:` has no special
  meaning. The parser does not support XML style namespaces via `:`.
- Scripts: This parser does not contain a JavaScript or a CSS
  interpreter. While one may be supplied, not all features will be
  supported.
- Rentrance: The current parser is not re-entrant. (Thus you can't pause
  the parser to modify the HTML string mid-parse.)
- Validation: The current tree builder is **not** a validating parser.
  While it will correct some HTML, it does not check that the HTML
  conforms to the standard. (Should you wish, you can build a validating
  parser by extending DOMTree or building your own EventHandler
  implementation.)
  * There is limited support for insertion modes.
  * Some autocorrection is done automatically.
  * Per the spec, many legacy tags are admitted and correctly handled,
    even though they are technically not part of HTML5.
- Attribute names and values: Due to the implementation details of the
  PHP implementation of DOM, attribute names that do not follow the 
  XML 1.0 standard are not inserted into the DOM. (Effectively, they
  are ignored.) If you've got a clever fix for this, jump in!
- Processor Instructions: The HTML5 spec does not allow processor
  instructions. We do. Since this is a server-side library, we think
  this is useful. And that means, dear reader, that in some cases you
  can parse the HTML from a mixed PHP/HTML document. This, however, 
  is an incidental feature, not a core feature.
- HTML manifests: Unsupported.
- PLAINTEXT: Unsupported.
- Adoption Agency Algorithm: Not yet implemented. (8.2.5.4.7)

## Thanks to...

The dedicated (and patient) contributors of patches small and large,
who have already made this library better.See the CREDITS file for
a list of contributors.

We owe a huge debt of gratitude to the original authors of html5lib.

While not much of the orignal parser remains, we learned a lot from
reading the html5lib library. And some pieces remain here. In
particular, much of the UTF-8 and Unicode handling is derived from the
html5lib project.

## License

This software is released under the MIT license. The original html5lib
library was also released under the MIT license.

See LICENSE.txt

Certain files contain copyright assertions by specific individuals
involved with html5lib. Those have been retained where appropriate.
