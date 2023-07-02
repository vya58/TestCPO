# TestCPO
Тестовое задание на вакансию Backend developer

# Техническое задание

Задание должно быть выполнено на PHP + Laravel.
1. Необходимо создать БД под управлением MySQL со следующими
сущностями:
а) Пользователь:
- id;
- Логин;
- Пароль;
- Имя;
- Фамилия;
- Дата регистрации;
- Дата рождения.
Все поля, кроме 'Дата рождения' не могут принимать нулевые значения
b) Event
- id;
- Заголовок;
- Текст;
- Дата создания;
- Создатель (сущность Пользователь);
- Участники (сущность Пользователи).
Все значения ненулевые.

2. Разработать RESTful API для:
а) регистрация пользователя;
b) авторизация пользователя;
c) создание события;
d) получение списка событий;
e) участие в событии;
f) отмена участия в событии;
g) удаление события создателем.
Ответ с сервера должен приходить в виде такого JSON: {"error":null, 
"result":{"id":1, "first_name":"Вася", "last_name":"Петров"}}.

3. Создать простую админку, используя AdminLTE:
- регистрация пользователя;
- авторизация пользователя;
- список событий;
- информация о пользователе.
Если в процессе регистрации или авторизации произошла ошибка, 
необходимо показать диалоговое окно с описанием ошибки. При
успешной регистрации или авторизации открывается окно со списком
событий (см. скрин).
При просмотре НЕ своего события внизу находится кнопка "Принять
участие", при просмотре своего события - кнопка "Отказаться от участия"
Элементы "Все события" и "Участники" должны обновляться каждые 30 
секунд, по возможности, без перезагрузки страницы. 
При клике на участника показывается экран информации об участнике в
произвольном виде.

Макет внешнего вида:

![Alt Макет внешнего вида](%D0%A2%D0%97-CPO.jpg)
