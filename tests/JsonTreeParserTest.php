<?php

use JsonLib\StreamingListener;
use JsonStreamingParser\Parser;
use PHPUnit\Framework\TestCase;


class JsonTreeParserTest extends TestCase
{
    /** @var resource */
    private $testTreeCase;

    private array $categoryNamesMap;

    protected function setUp()
    {
        $this->categoryNamesMap = [19 => 'Mydła', 21 => 'Do twarzy'];

        $this->testTreeCase = fopen('php://memory', 'w+');
        fwrite($this->testTreeCase, <<<JSON
            [
                {
                    "id": 19,
                    "children": [
                          {
                            "id": 20,
                            "children": []
                          },
                          {
                            "id": 21,
                            "children": []
                          }
                    ]
              }
            ]
        JSON);
        rewind($this->testTreeCase);
    }

    public function testIsValidJson()
    {
        $outputFileHandler = fopen('php://memory', 'w+');
        $listener = new StreamingListener($outputFileHandler);

        $parser = new Parser($this->testTreeCase, $listener);
        $parser->parse();

        rewind($outputFileHandler);
        $this->assertJson(fread($outputFileHandler, fstat($outputFileHandler)['size']));
    }

    public function testIsValidJsonAfterModification()
    {
        $outputFileHandler = fopen('php://memory', 'w+');
        $listener = new StreamingListener($outputFileHandler, function ($stream, string $key, $val) {
            if ($key === 'id' && key_exists($val, $this->categoryNamesMap)) {
                fwrite($stream,sprintf(",\"name\": \"%s\"", $this->categoryNamesMap[$val]));
            }
        });

        $parser = new Parser($this->testTreeCase, $listener);
        $parser->parse();

        rewind($outputFileHandler);
        $parsedJson = fread($outputFileHandler, fstat($outputFileHandler)['size']);
        $this->assertJson($parsedJson);

        $expectedJson = [
            0 => [
                'id' => 19,
                'name' => 'Mydła',
                'children' => [
                    0 => [
                        'id' => 20,
                        'children' => []
                    ],
                    1 => [
                        'id' => 21,
                        'name' => 'Do twarzy',
                        'children' => []
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedJson, json_decode($parsedJson, true));
    }
}
