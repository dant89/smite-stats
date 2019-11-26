![Smite logo](https://github.com/dant89/smite-stats/blob/master/public/images/LOGO_SMITE_2016_Blktagline_Shadow_500x170.png)

Smite Stats
============

This project is an unofficial open source [Smite](https://www.smitegame.com/) statistics project. The project uses the [Smite API](http://api.smitegame.com/smiteapi.svc) to populate clan and player statistics on a web front-end.

The [PHP Smite API client](https://github.com/dant89/smite-api-php-client) is used for all calls to the Hi-Rez Smite API. 

[Symfony 5](https://symfony.com/doc/current/setup.html) website-skeleton has been used for the base of the project.

## Installation

1. `git clone git@github.com:dant89/smite-stats.git`
2. `composer install`
3. `yarn install`
4. `yarn encore prod`
5. `cp .env .env.local`
6. Update `SMITE_DEV_ID` and `SMITE_AUTH_KEY` to reference your developer key.
6. Create a database and setup the config for the variable `DATABASE_URL`.

## Live Website
You can view this project running in production by visiting: [Smite Stats](https://smitestats.com/)

## Smite API Access

To request permission from Hi-Rez for access keys to use the Smite API, you must complete [this form](https://fs12.formsite.com/HiRez/form48/secure_index.html).
