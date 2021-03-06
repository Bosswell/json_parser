<?php

require_once 'vendor/autoload.php';

use JsonLib\JsonListExtractor;
use JsonLib\JsonTreeParser;

$treeFileHandler = fopen('data/tree.json', 'r');
$outputFileHandler = fopen('output/result.json', 'w+');
$listFileHandler = fopen('data/list.json', 'r');

$listExtractor = new JsonListExtractor($listFileHandler);
$parser = new JsonTreeParser(
    $treeFileHandler,
    $outputFileHandler,
    $listExtractor->getCategoryNamesMap()
);
$parser->parse();

//rewind($outputFileHandler);
//echo '<pre>' . print_r(fread($outputFileHandler, fstat($outputFileHandler)['size'])) . '</pre>';