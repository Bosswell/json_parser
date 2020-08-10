<h1>Zadanie 1</h1>

Sposób odpalenia:

1.Wykorzystaj dockera:
```console
docker build -t json_parser .
docker run --rm -v "$(pwd)"/output:/usr/src/json_parser/output --name parser_app json_parser
```

2.Bez dockera
Wymagana wersja: PHP >= 7.4
Wymagany composer

Załaduj zależności
```console
composer install
```

Odpal skrypt
```console
php index.php
```

Gotowy JSON umieszczony jest w pliku 'output/result.json'.<br>
W razie problemów z działaniem, sprawdź uprawnienia.
