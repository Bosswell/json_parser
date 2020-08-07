<?php

namespace JsonLib;

use JsonStreamingParser\Parser;
use Throwable;


class JsonTreeParser
{
    private array $categoryNamesMap;

    /** @var resource */
    private $treeFileHandler;

    /** @var resource */
    private $outputFileHandler;

    /**
     * @param resource $treeFileHandler
     * @param resource $outputFileHandler
     * @param array $categoryNamesMap
     * @throws Throwable
     */
    public function __construct($treeFileHandler, $outputFileHandler, array $categoryNamesMap)
    {
        $this->treeFileHandler = $treeFileHandler;
        $this->outputFileHandler = $outputFileHandler;
        $this->categoryNamesMap = $categoryNamesMap;
    }

    /**
     * @throws Throwable
     */
    public function parse(): void
    {
        $listener = new StreamingListener($this->outputFileHandler, function ($stream, string $key, $val) {
            if ($key === 'id' && key_exists($val, $this->categoryNamesMap)) {
                fwrite($stream,sprintf(",\"name\": \"%s\"", $this->categoryNamesMap[$val]));
            }
        });

        $parser = new Parser($this->treeFileHandler, $listener);
        $parser->parse();
    }
}
