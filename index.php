<?php
$loader = require 'plugins/autoload.php';
$dir = __DIR__;
$loader->add('MyApp\\', $dir.'/src/');

$parser = new \MyApp\PageParser\PageParser();

if ($argc == 1) {
    echo "Choose script to run or use help.\n";
    exit();
}else if ($argc == 2){
    if ($argv[1] == 'help') {
        $parser->help();
    }
    if ($argv[1] == 'report') {
        echo 'Please enter required param \'domain\'';
    }

}else if ($argc == 3){
    if ($argv[1] == 'parse') {
        $parser->parse($argv[2]);
    }
    if ($argv[1] == 'report') {
        $parser->report($argv[2]);
    }
}
