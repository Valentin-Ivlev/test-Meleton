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

