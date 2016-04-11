<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('tests')
    ->in(__DIR__ . '/src')
;

// generate documentation for all v2.0.* tags, the 2.0 branch, and the master one
$versions = GitVersionCollection::create(__DIR__)
    ->addFromTags('2.*')
    ->add('2.x', '2.0 branch')
    ->add('master', 'master branch')
;

return new Sami($iterator, array(
    'versions'             => $versions,
    'title'                => 'HTML5-PHP API',
    'build_dir'            => __DIR__.'/build/apidoc/%version%',
    'cache_dir'            => __DIR__.'/build/sami-cache/%version%',
    'default_opened_level' => 1,
    'remote_repository'    => new GitHubRemoteRepository('Masterminds/html5-php', dirname(__DIR__)),
));
