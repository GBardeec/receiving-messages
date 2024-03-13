## Setup instructions
### Requirements
1. PHP ^8.1
2. NPM ^6.13
3. MySQL DB

#### Laravel
- `composer install`
- `cp .env.example .env`
- modify **.env**
    1. Set **DB_***
- `php artisan migrate`

#### Vue
- `npm i`
- `npm run [dev/prod/watch]`

`php artisan serve` or **nginx** setup

#### Other
- The documentation for the API is made in scribe, it is located in ./public/docs/openapi.yaml
