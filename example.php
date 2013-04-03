<?php

require "vendor/autoload.php";


$html = "<html><head><title>TEST</title></head><body>Hello World</body></html>";

$dom = \HTML5\Parser::parse($html);

print_r($dom);
