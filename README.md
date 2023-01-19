# Laravel DDD & CQRS sample by Bejao

This project shows how to implement DDD & CQRS using Laravel Eloquent (Active Record).

This code is extracted from our internal project, so we are already using it.

Domain (Business)
-----------------
Is a very simple table booking example. Only the table entity is finished.
Has some events and event handlers.

How to test it
---------------
Is a clean Laravel Install, is not properly configured, although tests are working.

1. Download the project and use Sail to test it: ./vendor/bin/sail up -d
2. Enter in bash using ./vendorbin/bash
3. Run phpstan with composer phpstan
4. Run tests with php artisan test
