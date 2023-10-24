# Kizuner

## Developer Note

### Packges

All package bellow have some pre configuration, please check document for more information

- Telescope: Tracking application on Local, on Prod mode only Admin can track
- Passport: API Authentication
- spatie/laravel-permission: Save permission in DB so we can use later on other projects
- Compass: Api Document inside Application
- qoraiche/laravel-mail-editor: Mail Template Edit
- nunomaduro/larastan: Check Bug before it kill you
- Socialite: Auth using Social
- laravel-wallet: User Walled System

### Project init

- Step 0: Copy file .env.example to .env
- Step 1: ```composer update``` then ```yarn && yarn dev```
- Step 2: ```php artisan migrate``` then ```php artisan db:seed```, default credentials: admin@admin.com/admin
- Step 3: Run ``php artisan passport:install`` to to fix passport issue
- Step 4: ``php artisan git:install-hooks`` to install PSR2 auto check code quality before commit
- Step 5: ``./vendor/bin/phpstan analyse --memory-limit=2G`` To check your code Bug, if you have bad code please review and fix it, you can down level at phpstan.neon to 5

### Some Enterprise packages:

- https://github.com/matchish/laravel-scout-elasticsearch | Elasticsearch
- https://github.com/awes-io/navigator | Navigator
- https://bavix.github.io/laravel-wallet | Wallet
- https://docs.beyondco.de/laravel-websockets | Lara Socket
- https://github.com/Jimmy-JS/laravel-report-generator | Report
- https://laravel-excel.com/ | Excel
- https://github.com/spatie/laravel-fractal 
- https://docs.spatie.be/laravel-backup/v6/introduction/ | Backup


#### Before Commit and Push Code

By default, when commit it will auto check code quality, but you can to it manually:

- Please run command: ``php phpcs.phar --standard=PSR2 modules  --extensions=php`` and  ``php phpcs.phar --standard=PSR2 app  --extensions=php``  then fix all 
issue with code to make sure code follow PSR2 standard.

### Pipeline Setting for Merge Code

```bash
image: php:7.1.1
pipelines:
  pull-requests:
    '**': #this runs as default for any branch not elsewhere defined
      - step:
          script:
            - php -v
            - ls -la
            - echo "Kizuner PSR2 Code quality check"
            - php phpcs.phar --version
            - php phpcs.phar -n --standard=PSR2 app/  --extensions=php
            - php phpcs.phar -n --standard=PSR2 modules/  --extensions=php
```

## Deployment Note

First time:
- Create passport key: ``php artisan passport:keys``



