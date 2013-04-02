<?php

function run_all_tests($base) {
    foreach (glob("$base/*Test.php") as $file) {
        require_once $file;
    }
    foreach (glob("$base/*", GLOB_ONLYDIR) as $dir) {
        run_all_tests($dir);
    }
}

run_all_tests('.');

