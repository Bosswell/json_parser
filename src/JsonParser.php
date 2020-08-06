<?php

namespace JsonParser;

use Exception;
use JsonStreamingParser\Listener\InMemoryListener;
use JsonStreamingParser\Parser;
use Throwable;


class JsonParser
{
    private array $categoryNamesMap = [];

    /** @var resource */
    private $listFileHandler;

    /** @var resource */
    private $treeFileHandler;

    /** @var resource */
    private $outputFileHandler;

    private string $lang;

    /**
     * @param resource $listFileHandler
     * @param resource $treeFileHandler
     * @param resource $outputFileHandler
     * @param string $lang
     * @throws Throwable
     */
    public function __construct($listFileHandler, $treeFileHandler, $outputFileHandler, $lang = 'pl_PL')
    {
        $this->listFileHandler = $listFileHandler;
        $this->treeFileHandler = $treeFileHandler;
        $this->outputFileHandler = $outputFileHandler;
        $this->lang = $lang;

        $this->createCategoryNamesMap();
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

    /**
     * @throws Exception
     */
    private function createCategoryNamesMap(): void
    {
        $inMemoryListener = new InMemoryListener();
        $parser = new Parser($this->listFileHandler, $inMemoryListener);
        $parser->parse();

        foreach ($inMemoryListener->getJson() as $entry) {
            if (!key_exists('category_id', $entry)) {
                throw new Exception('Key [category_id] need to be specified');
            }

            $this->categoryNamesMap[$entry['category_id']] = $entry['translations'][$this->lang]['name'] ?? 0;
        }
    }
}
