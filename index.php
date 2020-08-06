<?php

use JsonParser\JsonParser;

require_once 'vendor/autoload.php';

$treeFileHandler = fopen('data/tree.json', 'r');
$outputFileHandler = fopen('output/result.json', 'w+');
$listFileHandler = fopen('data/list.json', 'r');


$parser = new JsonParser($listFileHandler, $treeFileHandler, $outputFileHandler);
$parser->parse();
