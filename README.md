# Тестовое задание
## Задание 1
```sql
SELECT u.id,
    CONCAT(u.first_name, ' ', u.last_name) as Name,
    b.author as Author,
    (SELECT GROUP_CONCAT(name)
     FROM books b
        LEFT JOIN user_books ub on b.id = ub.book_id
     WHERE u.id = ub.user_id) as Books
FROM users u
    LEFT JOIN user_books ub ON u.id = ub.user_id
    LEFT JOIN books b       ON b.id = ub.book_id
WHERE TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 7 AND 17
    AND DATEDIFF(ub.return_date, ub.get_date) <= 14
    AND ub.user_id IN (
       SELECT ub1.user_id
       FROM user_books ub1
           LEFT JOIN books b1 ON ub1.book_id = b1.id
       GROUP BY b1.author, ub1.user_id
       HAVING COUNT(b1.author) = 2
   )
GROUP BY u.id
;
```
## Задание 2
### Развёртывание и запуск проекта
```shell
make init
make up
```
Если на хосте не устоновлена утилита make, то нужно выполнить целевые команды для следующих make комманд, описанных в Makefile в корне проекта, в порядке их следования в команде init.
```shell
docker-compose build
docker-compose run --rm php-cli mkdir runtime/logs
docker-compose run --rm php-cli chmod -R 777 web runtime views models controllers migrations
docker-compose run --rm php-cli composer install
docker-compose up -d
```
### Тестирование
Api доступно по адресу `http://localhost:8808`

Путь `/api/v1?method=<method>&...`

методы:
* rates (GET)
* convert (POST)
  
параметры согласно заданию.

**Авторизация** Bearer.

Для запросов можно использовать след. токены:
* 100-token
* 101-token

________
### Текст Заданий
#### Задание 1

**Mysql**

Имеется три таблицы:
users
id
first_name
last_name
birthday
1
Ivan
Ivanov
2005-01-01
2
Marina
Ivanova
2011-03-01


books
id
name
author
1
Romeo and Juliet
William Shakespeare
2
War and Peace
Leo Tolstoy


user_books
id
user_id
book_id
get_date
return_date
1
1
2
2022-01-01
2022-02-01
2
2
1
2021-01-01
2022-01-01


Необходимо написать запрос выборки данных из представленных таблиц, который найдет и выведет всех посетителей библиотеки, возраст которых попадает в диапазон от 7 и до 17 лет, которые  взяли две книги одного автора (взяли всего 2 книги и они одного автора), книги были у них в руках не более двух календарных недель (не просрочили 2-х недельный срок пользования).
Формат вывода:
ID, Name (first_name  last_name), Author, Books (Book 1, Book 2, ...)
1; Ivan Ivanov; Leo Tolstoy; Book 1, Book 2

#### Задание 2

**PHP**

Необходимо реализовать JSON API сервис на  языке php 8 (можно использовать любой php framework) для работы с курсами обмена валют (относительно USD). Реализовать необходимо с помощью Docker.

Сервис для получения текущих курсов валют: https://api.coincap.io/v2/rates

Все методы API будут доступны только после авторизации, т.е. все методы должны быть по умолчанию не доступны и отдавать ошибку авторизации.

Для авторизации будет использоваться фиксированный токен (64 символа включающих в себя a-z A-Z 0-9 а так-же символы - и _ ), передавать его будем в заголовках запросов. Тип Authorization: Bearer.

Формат запросов: <your_domain>/api/v1?method=<method_name>&<parameter>=<value>

Формат ответа API: JSON (все ответы при любых сценариях должны иметь JSON формат)

Все значения курса обмена должны считаться учитывая нашу комиссию = 2%



API должен иметь 2 метода:

rates: Получение всех курсов с учетом комиссии = 2% (GET запрос) в формате:
{
“status”: “success”,
“code”: 200,
“data”: {
“USD” : <rate>,
...
}
}

В случае ошибки:
{
“status”: “error”,
“code”: 403,
“message”: “Invalid token”
}

Сортировка от меньшего курса к большему курсу.

В качестве параметров может передаваться интересующая валюта, в формате USD,RUB,EUR и тп В этом случае, отдаем указанные в качестве параметра currency значения.

convert: Запрос на обмен валюты c учетом комиссии = 2%. POST запрос с параметрами:
currency_from: USD
currency_to: BTC
value: 1.00

или в обратную сторону

currency_from: BTC
currency_to: USD
value: 1.00

В случае успешного запроса, отдаем:

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

В случае ошибки:
{
“status”: “error”,
“code”: 403,
“message”: “Invalid token”
}

Важно, минимальный обмен равен 0,01 валюты from
Например: USD = 0.01 меняется на 0.0000005556 (считаем до 10 знаков)
Если идет обмен из BTC в USD - округляем до 0.01