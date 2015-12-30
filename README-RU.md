# silex-mvc

Простой фреймворк реализующий шаблон проектирования - MVC на базе SILEX (PHP микро-фреймворка,
созданного на основе Symfony2 компонентов). Некоторые идеи мной были заимствованы с проекта
[GitHub (silex-enhanced)](https://github.com/FluencyLabs/silex-enhanced-skeleton). 
С документацией по SILEX можно познакомиться на сайте (http://silex.sensiolabs.org). 
Примеры установки фреймворка приведены для ОС "Windows"
и веб сервера Nginx. 

Основные характеристики фреймворка:

- фреймворк реализует простое приложение управления блогом;
- расширяется с помощью конфигурационных файлов, расположенных в `app/Resources/Сonfig`;
- работает как веб или как консольное приложение;
- с помощью консольного приложения можно выполнять разные служебные задачи (пр. создание БД `app/Console/scripts/orm/schema_create.bat`);
- для примера с помощью консольного приложения реализована работа с сервисом UBKI (http://ubki.ua/ru);
- реализована локализация для двух языков: английский, русский;
- обеспечивается процесс регистрации, аутентификации и авторизации пользователей;
- в БД созданы два пользователя с соответствующими правами. Администратор (login=admin; pass=foo) User (login=user; pass=foo);
- использует БД типа SqlLite `app/Resources/db/app.db`;
- работа с БД обеспечивается с помощью Doctrine(DBAL, ORM) и PHP ActiveRecord;
- добавлены сервисы такие как Pagerfanta, Zend-Filter, Zend-Json и др. `vendor/library`;
- так же добавлены сервисы для работы с массивами, строками, XML, HTTP, Markdown `app/Services/My`;
- на стороне клиента используются библиотеки : jQuery, Bootstrap 3, RequireJS, Backbone `public/js/lib`;
- на стороне клиента используются сервисы: Datepicker, FormValidation, MaskInput, Highlight `public/js/app/services`;
- для примера работы фреймворка Backbone реализовано приложение `ToDo` для локального или серверного хранилищ данных `public/js/app/bb-todo`.


## Инсталяция

### Предварительные требования

- [PHP](http://php.net) version >= 5.4
- веб сервер Nginx или Apache2
- Composer

### Развертывание

1. Клонировать [silex-mvc](https://github.com/alvk4r/silex-enhanced) проект с помощью git.
2. Выполнить `composer install`.
3. Для создания базы данных выполнить командный файл из консоли `app/Console/scripts/orm/schema_create.bat`, 
предварительно отредактировав пути к `php.exe` и к `app\Console\index.php`;
4. Сконфигурируйте веб сервер, что бы точка входа была `public/index.php`.
5. Установите, если необходимо, соответсвующие права на запись в `path/to/project/var`.
6. Введите адрес сайта в броузер (пр. http://silex-mvc/)

## Конфигурация

### config.yml
Все общие конфигурации используются как веб приложением так и консольным приложением.

```yaml
 service_providers:
    swiftmailer:
        class: Silex\Provider\SwiftmailerServiceProvider
        parameters:
            swiftmailer.options:
                host: %mail.host%
                port: %mail.port%
                username: %mail.username%
                password: %mail.password%
                encryption: %mail.encryption%
                auth_mode: %mail.auth_mode%
...
```

### console.yml или application.yml
Конфигурации соответственно применяются для консольного и веб приложений.
Если вы используете в ваших конфигурациях выражение `imports`, 
то ваш конфиг будет рекурсивно объединен с конфигом указанным в выражении `imports`

### parameters.yml
Здесь устанавливаются значения основных параметров вашей конфигурации, 
которые могут использоваться в других ваших конфигурациях. 
Значения параметров можно получить через `$app['config']['parameters']`

### security.yml
Здесь устанавливаются значения способа аутентификации пользователя и его авторизации.
С подробностями можно познакомиться [сдесь](http://silex.sensiolabs.org/doc/providers/security.html).

### app.ini
Для увеличения быстродействия веб приложения можно сконфигурировать приложение
с помощью `ini` файла.