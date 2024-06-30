## Задание 1

<i>
Имеется три таблицы:
users (id, first_name, last_name, birthday)
books (id, name, author)
user_books (id, user_id, book_id, get_date, return_date)

Необходимо написать запрос на MySQL выборки данных из представленных таблиц, который найдет и выведет всех посетителей библиотеки, возраст которых попадает в диапазон от 7 и до 17 лет, которые взяли две книги одного автора (взяли всего 2 книги и они одного автора), книги были у них в руках не более двух календарных недель (не просрочили 2-х недельный срок пользования).

Формат вывода:<br>
ID, Name (first_name last_name), Author, Books (Book 1, Book 2, ...)
</i>

### Решение

````sql
SELECT 
    u.id,
    CONCAT(u.first_name, ' ', u.last_name) AS Name,
    b.author AS Author,
    GROUP_CONCAT(b.name ORDER BY b.name SEPARATOR ', ') AS Books
FROM 
    users u
    JOIN user_books ub ON u.id = ub.user_id
    JOIN books b ON ub.book_id = b.id
WHERE
    TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 7 AND 17
GROUP BY 
    u.id, b.author
HAVING 
    COUNT(DISTINCT b.id) = 2 AND 
    MAX(DATEDIFF(ub.return_date, ub.get_date)) <= 14;
````
## Задание 2

<i>
Необходимо реализовать JSON API сервис на языке php 8 (можно использовать
Laravel framework) для работы с курсами обмена валют для биткоина (BTC).

Реализовать необходимо с помощью Docker.

Сервис для получения текущих курсов валют: https://blockchain.info/ticker

Все методы API будут доступны только после авторизации, т.е. все методы должны
быть по умолчанию не доступны и отдавать ошибку авторизации.

Для авторизации будет использоваться фиксированный токен (64 символа
включающих в себя a-z A-Z 0-9 а так-же символы - и _ ), передавать его будем в
заголовках запросов. Тип Authorization: Bearer.

Формат запросов:

<your_domain>/api/v1?method=<method_name>&<parameter>=<value>

Формат ответа API: JSON (все ответы при любых сценариях должны иметь JSON
формат)

Все значения курса обмена должны считаться учитывая нашу комиссию = 2%
API должен иметь 2 метода:

1) rates: Получение всех курсов с учетом комиссии = 2% (GET запрос) в формате:
````json
{
    “status”: “success”,
    “code”: 200,
    “data”: {
        “USD” : <rate>,
        ...
    }
}
````
В случае ошибки:
````json
{
    “status”: “error”,
    “code”: 403,
    “message”: “Invalid token”
}
````
Сортировка от меньшего курса к большему курсу.

В качестве параметров может передаваться интересующая валюта, в формате
USD,RUB,EUR и тп В этом случае, отдаем указанные в качестве параметра
currency значения.

2) convert: Запрос на обмен валюты c учетом комиссии = 2%. POST запрос с
   параметрами:
````json
currency_from: USD
currency_to: BTC
value: 1.00
или в обратную сторону
currency_from: BTC
currency_to: USD
value: 1.00
````
В случае успешного запроса, отдаем:
````json
{
    “status”: “success”,
    “code”: 200,
    “data”: {
        “currency_from” : BTC,
        “currency_to” : USD,
        “value”: 1.00,
        “converted_value”: 1.00,
        “rate” : 1.00,
    }
}
````
В случае ошибки:
````json
{
    “status”: “error”,
    “code”: 403,
    “message”: “Invalid token”
}
````
Важно, минимальный обмен равен 0,01 валюты from
Например: USD = 0.01 меняется на 0.0000005556 (считаем до 10 знаков)

Если идет обмен из BTC в USD - округляем до 0.01
</i>
### Развертывание проекта

Клонируйте проект:
````bash
git clone https://github.com/Valentin-Ivlev/test-Meleton.git
````
перейдите в папку проекта:
````bash
cd test-Meleton
````
соберите и запустите контейнеры:
````bash
docker-compose up -d --build
````
выполните скрипт настройки:
````bash
docker-compose exec app bash -c "chmod +x setup.sh && ./setup.sh"
````
### Проверка API
Проект будет доступен по адресу:
````bash
http://localhost:8000
````
токен авторизации:
````bash
abcABC12345-67890_abcdefghijklmnop-qrstuvwxyz_ABCDEFGHIJKLMNOPQ
````
его можно поменять в файле:
````bash
app/Http/Middleware/ApiTokenMiddleware.php
````
Для проверки методов API можно использовать, например Postman:
1. Метод rates:

создаем в Postman новый GET запрос с адресом:
````bash
http://localhost:8000/api/v1?method=rates
````
добавляем токен авторизации (вкладка Authorization -> в выпадающем списке Auth Type выбираем Bearer Token и в поле Token вводим токен авторизации)

Метод rates с параметром:

создаем в Postman новый GET запрос с адресом:
````bash
http://localhost:8000/api/v1?method=rates&currency=USD,EUR,RUB
````
добавляем токен авторизации (вкладка Authorization -> в выпадающем списке Auth Type выбираем Bearer Token и в поле Token вводим токен авторизации)

2. Метод convert:

создаем в Postman новый POST запрос с адресом:
````bash
http://localhost:8000/api/v1?method=convert
````
добавляем токен авторизации (вкладка Authorization -> в выпадающем списке Auth Type выбираем Bearer Token и в поле Token вводим токен авторизации)

добавляем параметры запроса (вкладка Body -> выбираем x-www-form-urlencoded и добавляем ключи и значения: currency_from = BTC, currency_to = USD и value = 1)
