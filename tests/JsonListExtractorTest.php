<?php

use JsonLib\JsonListExtractor;
use PHPUnit\Framework\TestCase;


class JsonListExtractorTest extends TestCase
{
    /** @var resource */
    private $testCasePositive;

    /** @var resource */
    private $testCaseWithoutCategoryId;

    /** @var resource */
    private $testCaseWithoutNameKey;

    /** @var resource */
    private $testCaseWithoutAnyTranslations;

    protected function setUp()
    {
        $this->testCasePositive = fopen('php://memory', 'r+');
        $this->testCaseWithoutCategoryId = fopen('php://memory', 'r+');;
        $this->testCaseWithoutNameKey = fopen('php://memory', 'r+');
        $this->testCaseWithoutAnyTranslations = fopen('php://memory', 'r+');

        fwrite($this->testCasePositive, <<<JSON
            [
                {
                    "category_id": "1",
                    "in_loyalty": "0",
                    "translations": {
                        "pl_PL": {
                            "category_id": "1",
                            "name": "Kobiety",
                            "items": 1,
                            "attribute_groups": [
                                1,
                                2
                            ]
                        }
                    }
                },
                {
                    "category_id": "5",
                    "in_loyalty": "0",
                    "translations": {
                        "pl_PL": {
                            "category_id": "5",
                            "name": "Spódnice",
                            "items": 1,
                            "attribute_groups": []
                        }
                    }
                }
            ]
        JSON);

        fwrite($this->testCaseWithoutCategoryId, <<<JSON
            [
                {
                    "in_loyalty": "0",
                    "translations": {
                        "pl_PL": {
                            "category_id": "1",
                            "items": 1,
                            "attribute_groups": [
                                1,
                                2
                            ]
                        }
                    }
                },
                {
                    "in_loyalty": "0",
                    "translations": {
                        "pl_PL": {
                            "category_id": "1",
                            "name": "Spódnice",
                            "items": 1,
                            "attribute_groups": []
                        }
                    }
                }
            ]
        JSON);

        fwrite($this->testCaseWithoutNameKey, <<<JSON
            [
                {
                    "category_id": "1",
                    "in_loyalty": "0",
                    "translations": {
                        "pl_PL": {
                            "category_id": "1",
                            "items": 1,
                            "attribute_groups": [
                                1,
                                2
                            ]
                        }
                    }
                },
                {
                    "category_id": "5",
                    "in_loyalty": "0",
                    "translations": {
                        "pl_PL": {
                            "category_id": "5",
                            "name": "Spódnice",
                            "items": 1,
                            "attribute_groups": []
                        }
                    }
                }
            ]
        JSON);

        fwrite($this->testCaseWithoutAnyTranslations, <<<JSON
            [
                {
                    "category_id": "1",
                    "in_loyalty": "0"
                },
                {
                    "category_id": "5",
                    "in_loyalty": "0",
                    "translations": {}
                }
            ]
        JSON);

        rewind($this->testCasePositive);
        rewind($this->testCaseWithoutNameKey);
        rewind($this->testCaseWithoutCategoryId);
        rewind($this->testCaseWithoutAnyTranslations);
    }

    public function testListIsCorrectlyParsed()
    {
        $parser = new JsonListExtractor($this->testCasePositive);
        $expectedOutput = [5 => 'Spódnice', 1 => 'Kobiety'];

        $this->assertEquals($expectedOutput, $parser->getCategoryNamesMap());
    }

    public function testParseListWithoutCategoryIdKey()
    {
        $this->expectException(Throwable::class);
        new JsonListExtractor($this->testCaseWithoutCategoryId);
    }

    public function testParseListWithoutTranslationName()
    {
        $parser = new JsonListExtractor($this->testCaseWithoutNameKey);
        $expectedOutput = [5 => 'Spódnice'];

        $this->assertEquals($parser->getCategoryNamesMap(), $expectedOutput);
    }

    public function testParseListWithoutAnyTranslations()
    {
        $parser = new JsonListExtractor($this->testCaseWithoutAnyTranslations, 'de_DE');

        $this->assertEquals($parser->getCategoryNamesMap(), []);
    }
}