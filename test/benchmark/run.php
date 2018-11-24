<?php

require __DIR__ . '/../../vendor/autoload.php';

$iterations = isset($argv[1]) ? $argv[1] : 100;

$html5 = new Masterminds\HTML5();
$content = file_get_contents(__DIR__ . '/example.html');
$dom = $html5->loadHTML($content);

$samples = array();
for ($i = 0; $i < $iterations; ++$i) {
    $t = microtime(true);
    $dom = $html5->loadHTML($content);
    $samples[] = microtime(true) - $t;
}
$time = array_sum($samples) / count($samples);
echo 'Loading: ' . ($time * 1000) . "\n";

$samples = array();
for ($i = 0; $i < $iterations; ++$i) {
    $t = microtime(true);
    $html5->saveHTML($dom);
    $samples[] = microtime(true) - $t;
}
$time = array_sum($samples) / count($samples);
echo 'Writing: ' . ($time * 1000) . "\n";

exit(0);
