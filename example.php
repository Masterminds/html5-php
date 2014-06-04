<?php

require "vendor/autoload.php";


$html = <<< 'HERE'
  <html>
  <head>
  <title>TEST</title>
  <script language="javascript">
  if (2 > 1) { alert("Math wins."); }
  </script>
  </head>
  <body id='foo'>
  <!-- This space intentionally left blank. -->
  <section class="section-a pretty" id="bar1">
  <h1>Hello World</h1><p>This is a test of the HTML5 parser.</p>
  <hr>
  &amp; Nobody nowhere.
  </section>
  <test xmlns:foo="http://example.com/foo">TEST</test>
  <![CDATA[Because we can.]]>
  &copy;
  </body></html>
HERE;

$dom = \HTML5Helper::loadHTML($html);

print "Converting to HTML 5\n";

\HTML5Helper::save($dom, fopen("php://stdin", 'w'));
