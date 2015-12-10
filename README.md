# silex-mvc 

Simple framework implements a design pattern - MVC-based SILEX (PHP micro-framework,
created by Symfony2 components). The documentation can be found on the SILEX
Online (http://silex.sensiolabs.org). Examples of installation of the framework given for OS "Windows"
and web server Nginx.

Main features of the framework:

- Framework implements a simple application management blog.
- Expands with configuration files located in the `app/Resources/Сonfig`.
- It works as a web or as a console application.
- Console application can perform various tasks (e.g. Creation of a database `app/Console/scripts/orm/schema_create.bat`).
- As an example, the work of the console application implemented to work with the service [UBKI](http://ubki.ua/ru).
- Realized the localization of two languages: English and Russian.
- Ensures the registration process, user authentication and authorization.
- The database created two users with the appropriate rights. The administrator (login = admin; pass = foo) User (login = user; pass = foo).
- Uses a database type SqlLite `app/Resources/db/app.db`.
- Work with databases provided by Doctrine(DBAL, ORM) and и PHP ActiveRecord.
- Added services such as the Pagerfanta, Zend-Filter, Zend-Json and others. `app/library`.
- Added services for working with arrays, strings, XML, HTTP, Markdown `app/Services/My`.
- On the client side using the library: jQuery, Bootstrap 3, RequireJS, Backbone `public/js/lib`.
- Client-side services are used: Datepicker, FormValidation, MaskInput, Highlight `public/js/app/services`.
- `ToDo` implemented application (for the local or server storage) to show the work of the framework Backbone `public/js/app/bb-todo`.

## Installing

### Prerequisites

- [PHP](http://php.net) version >= 5.4
- Apache2, Nginx web server or similar
- Composer

### Deploying

1. Clone [silex-mvc](https://github.com/bsa-git/silex-mvc) project with git.
2. Run `composer install`.
3. To create a database run the batch file from the console `app/Console/scripts/orm/schema create.bat` 
   pre-editing the path to` php.exe` and `app\Console\index.php`;
4. Configure the web server so that the entry point was `public/index.php`.
5. Set, if necessary, the appropriate permissions to write to `path/to/project/var`.
6. Access your project url with web browser.

## Configuration

### config.yml
All common configs shared by both, console and web applications. Put your service
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

### console.yml \& application.yml
The console and web bootstrap config respectively. If you use `imports` statement
the config will be merged recursively allows partial specific configs.

### parameters.yml
Values for config parameters substitution. On application code parameters are 
accessible through `Silex\Application` instance `$app['config']['parameters']`.

### security.yml
The security firewalls and access control config. All information about it's available
[here](http://silex.sensiolabs.org/doc/providers/security.html).

### app.ini
To increase the performance of web applications, you can configure the application
through `ini` file.