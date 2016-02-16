# silex-mvc 

Simple framework implements a design pattern MVC, based SILEX (PHP micro-framework,
created by Symfony2 components). I have taken some ideas from the project 
[GitHub (silex-enhanced)](https://github.com/FluencyLabs/silex-enhanced-skeleton).
The documentation can be found on the [SILEX Online](http://silex.sensiolabs.org). 
Examples of installation of the framework given for OS "Windows" and web server Nginx.

Main features of the application:

- application implements a simple application management blog.
- expands with configuration files located in the `app/Resources/Сonfig`.
- it works as a web or as a console application.
- console application can perform various tasks (e.g. Creation of a database `app/Console/scripts/orm/schema_create.bat`).
- as an example, the work of the console application implemented to work with the service [UBKI](http://ubki.ua/ru).
- realized the localization of two languages: English and Russian.
- ensures the registration process, user authentication and authorization.
- the database created two users with the appropriate rights. The Administrator (login = admin; pass = foo), User (login = user; pass = foo).
- uses a database type SqlLite `app/Resources/db/app.db`.
- work with databases provided by Doctrine(DBAL, ORM) `vendor/doctrine` and PHP ActiveRecord `vendor/library/AR`.
- realized the possibility output the pages using the library Pagerfanta `vendor/library/Pagerfanta`.
- used template Twig `vendor/twig`.
- library of SwiftMailer is used for email `vendor/swiftmailer`. 
- added services such as Zend-Filter, Zend-Json and others `vendor/zendframework`.
- added services for working with arrays, strings, XML, HTTP, Markdown `app/Services/My`.
- on the client side using the library: jQuery, Bootstrap 3, RequireJS, Backbone `public/js/lib`.
- client-side services are used: Datepicker, FormValidation, MaskInput, Highlight `public/js/app/services`.
- `ToDo` implemented application (for the local or server storage) to show the work of the framework Backbone `public/js/app/bb-todo`.

## Installing

### Prerequisites

- [PHP](http://php.net) version >= 5.4
- Apache2, Nginx web server or similar
- Composer

### Deploying

1. Clone [silex-mvc](https://github.com/bsa-git/silex-mvc) project with git.
2. Run `composer install`.
3. To create a database run the batch file from the console `app/Console/scripts/orm/schema_create.bat` 
   pre-editing the path to` php.exe` and `app\Console\index.php`;
4. Configure the web server so that the entry point was `public/index.php`.
5. Set, if necessary, the appropriate permissions to write to `path/to/project/var`.
6. Access your project url with web browser.

## Configuration

### parameters.yml
Values for config parameters substitution. On application code parameters are 
accessible through `Silex\Application` instance `$app['config']['parameters']`.

### config.yml
General configuration is used as a Web application and a Сonsole application. Put your service
providers under `service_providers` section.

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

### console.yml or application.yml
The console and web bootstrap config respectively. If you use `imports` statement
the config will be merged recursively allows partial specific configs.

### security.yml
The security firewalls and access control config. All information about it's available
[here](http://silex.sensiolabs.org/doc/providers/security.html).

### app.ini
To increase performance, a web application can be configured
using `ini` file.