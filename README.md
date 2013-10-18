Translation Keys Service (TKS)
==============================

German project to allow an user interface to administrate the translations of the CRM
The project has been built using the following technologies:

  * Backbone, javascript
  * Symfony2 PHP
  * UnderscoreJS ( + their template engine ) 

Key aspects of development:

  * Single Page Application, no page reloads
  * All the JS code follows the MVC pattern
  * Template system, no HTML in generated "in-line"
  * Comunication between server and client is done using AJAX through REST calls
  * Complete design of the REST API
  * Different levels of permissions per user
  * External API can be called using APIKeys, using a shared secret to update de DB
  * Well written code, it can scale pretty easily

Code
----

The JS Backbone code can be found in the following directory:

    src/Tks/TksBundle/Resources/public/js/

The Symfony's Controllers and REST API can be found here:

    src/Tks/TksBundle/Controller/

Installing the Standard Edition
-------------------------------

When it comes to installing the Symfony Standard Edition, you have the
following options.

### Use Composer (*recommended*)

As Symfony uses [Composer][1] to manage its dependencies, the recommended way
to create a new project is to use it.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

Then, use the `create-project` command to generate a new Symfony application:

    php composer.phar create-project symfony/framework-standard-edition path/to/install

Composer will install Symfony and all its dependencies under the
`path/to/install` directory.

### Download an Archive File

To quickly test Symfony, you can also download an [archive][2] of the Standard
Edition and unpack it somewhere under your web server root directory.

If you downloaded an archive "without vendors", you also need to install all
the necessary dependencies. Download composer (see above) and run the
following command:

    php composer.phar install

Enjoy!

[1]:  http://getcomposer.org/
[2]:  http://symfony.com/download