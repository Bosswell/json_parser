<h1>Zadanie 1</h1>

Wymaga wersja: PHP >= 7.4.

Załaduj zależności
```console
composer install
```

Odpal skrypt
```console
php index.php
```

Wykorzystaj jako niezależny komponent

```php

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
```

Gotowy JSON umieszczony jest w pliku 'output/result.json'.<br>
W razie problemów z działaniem, sprawdź uprawnienia.
