![Smite logo](https://github.com/dant89/smite-stats/blob/master/public/images/LOGO_SMITE_2016_Blktagline_Shadow_500x170.png)

Smite Stats
============

This project is an unofficial open source Smite statistics project. The project uses the [Smite API](http://api.smitegame.com/smiteapi.svc) to populate clan and player statistics on a web front-end.

The [PHP Smite API client](https://github.com/dant89/smite-api-php-client) is used for all calls to the Hi-Rez Smite API. 

[Symfony 5](https://symfony.com/doc/current/setup.html) framework has been used for the base of this project.

## Installation

There is a Docker compose script ready to run, this builds containers for `php-fpm`, `nginx`, `mysql` and `redis`.

1. `git clone git@github.com:dant89/smite-stats.git`
2. `cd smite-stats`
3. `sudo cp .env .env.local`
4. Update `SMITE_DEV_ID` and `SMITE_AUTH_KEY` in `.env.local` to reference your Smite API developer key.
5. Add a new line to your `/etc/hosts` file containing `127.0.0.1       smitestats.devvm`
6. `docker-compose build`
7. `docker-compose up -d`
8. `docker-compose exec php-fpm composer install`
9. `docker-compose exec php-fpm php bin/console doctrine:migrations:migrate`
10. `docker-compose exec php-fpm yarn install`
11. `docker-compose exec php-fpm yarn encore prod`
12. Browse to http://smitestats.devvm

## (deprecated) Live Website

[Smite Stats](https://smitestats.com/) was unfortunately deprecated. Sadly the size of the database and processing power required was too expensive to run as a hobby.

## Smite API Access

To request permission from Hi-Rez for access keys to use the Smite API, you must complete [this form](https://fs12.formsite.com/HiRez/form48/secure_index.html).
