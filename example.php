<?php

require "vendor/autoload.php";


$html = <<< 'HERE'
  <html><head><title>TEST</title></head>
  <body id='foo'>
  <!-- This space intentionally left blank. -->
  <section class="section-a pretty" id="bar1">
  <h1>Hello World</h1><p>This is a test of the HTML5 parser.</p>
  <hr>
  </section>
  <![CDATA[Because we can.]]>
  </body></html>
HERE;

$dom = \HTML5::parse($html);

print "Converting to HTML 5\n";

\HTML5::save($dom, fopen("php://stdin", 'w'));
