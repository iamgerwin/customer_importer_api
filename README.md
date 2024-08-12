# Customer Importer API
## Coding Challenge Checklist
- ✅ The database layer MUST be [Doctrine](http://www.doctrine-project.org/projects/orm.html)
- ✅ The database MUST only store the information that is needed for this task.
- ✅ The customer’s clear password from the 3rd party API MUST be hashed using the md5 algorithm. DO NOT use the already hashed value from the API.
- ✅ Importer logic MUST be reusable in different parts of the application without any code changes in the importer itself.
- ✅ Proper exception handling MUST be considered.
- ✅ Tests MUST make sure to not request the real third party API.
- ✅ Tests MUST validate response structure.
- ✅ Tests MUST have both positive and negative outcomes.
- ✅ Config files MUST be utilized for values that might change in case of requirements changes
- ✅ Code logic MUST be decoupled following single responsibility principle
- ✅ Code MUST only contain necessary files or classes. Boilerplate code must be removed.
- ✅ Code MUST be submitted in a GitHub repository.

## Technical requirements
- PHP 8.2 or higher
- [Composer](https://getcomposer.org/download/)
- [Symfony CLI](https://symfony.com/download) 
- preferred DB: MariaDB or MySQL or PostgreSQL or SQLite
## Project setup
1. `git clone git@github.com:iamgerwin/customer_importer_api.git`
2. `cd customer_importer_api`
3. `composer install`
# DB setup
1. ensure .env file contains correct credentials to connect on db server of choice
 `DATABASE_URL="mysql://[DB_USER]:[DB_PASS]@127.0.0.1:3306/[DB_NAME]?serverVersion=mariadb-10.5.8"`
2. if no existing db name exists.
to create new db, run:  `php bin/console doctrine:database:create`
3. to run pending migrations: `php bin/console doctrine:migrations:migrate`
## Import customers from randomuser api
1. once db setup is done, run: `php bin/console app:customer:import`
   - it will ask how many customers you need to input (default & minimum value is 100)
   - it will ask what nationality you want (default is AU) (choices: AU, BR, CA, CH, DE, DK, ES, FI, FR, GB, IE, IN, IR, MX, NL, NO, NZ, RS, TR, UA, US) 
## Unit tests
1. run `./vendor/bin/phpunit`

## API
1.  run: `symfony server:start`
2. `/customers` - list of all customers
3. `/customers/[ID]` - show specific customer