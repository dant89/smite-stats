![Smite logo](https://github.com/dant89/smite-stats/blob/master/public/images/LOGO_SMITE_2016_Blktagline_Shadow_500x170.png)

Smite Stats
============

A playground for testing the [Smite API](http://api.smitegame.com/smiteapi.svc) to populate clan and player statistics on a web front-end.

The project uses a [Symfony 4](https://symfony.com/doc/current/setup.html) website-skeleton for simplicity.

Currently the project also includes a PHP Smite API client, at some point when I've built on it further I will extract this into a separate composer component.

## Installation

1. `git clone git@github.com:dant89/smite-stats.git`
2. `composer install`
3. `yarn install`
4. `yarn encore prod`
5. `cp .env .env.local`
6. Update `SMITE_DEV_ID` and `SMITE_AUTH_KEY` to reference your developer key.

## Smite API Access

To be able to use the Smite API you must complete [this form](https://fs12.formsite.com/HiRez/form48/secure_index.html) to request permission for access keys.
