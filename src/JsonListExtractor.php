<?php

namespace JsonLib;

use Exception;
use JsonStreamingParser\Listener\InMemoryListener;
use JsonStreamingParser\Parser;
use Throwable;


class JsonListExtractor
{
    private array $categoryNamesMap = [];

    /** @var resource */
    private $listFileHandler;

    private string $lang;

    /**
     * @param resource $listFileHandler
     * @param string $lang
     * @throws Throwable
     */
    public function __construct($listFileHandler, $lang = 'pl_PL')
    {
        $this->listFileHandler = $listFileHandler;
        $this->lang = $lang;

        $this->extract();
    }

    public function getCategoryNamesMap(): array
    {
        return $this->categoryNamesMap;
    }

    /**
     * @throws Exception
     */
    private function extract()
    {
        $inMemoryListener = new InMemoryListener();
        $parser = new Parser($this->listFileHandler, $inMemoryListener);
        $parser->parse();

        foreach ($inMemoryListener->getJson() as $entry) {
            if (!key_exists('category_id', $entry)) {
                throw new Exception('Key [category_id] need to be specified');
            }

            if ($name = $entry['translations'][$this->lang]['name'] ?? null) {
                $this->categoryNamesMap[$entry['category_id']] = $name;
            }
        }
    }
}
