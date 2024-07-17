# Flexisource IT PHP Exam
Exam Submission by Dexter Loor (dexter.loor@gmail.com) for the position of Sr. PHP Developer

## Requirements:
- PHP v8.1 or higher
- MySQL v5.7 or higher
- Composer
- Symfony CLI (refer to Step 1 of this page: https://symfony.com/download to install Symfony CLI)

Note: If you do not have composer installed globally, follow these instructions:

- Download Composer: https://getcomposer.org/download/
- After running the installer following the Download page instructions you can run this to move composer.phar to a directory that is in your path:
  `mv composer.phar /usr/local/bin/composer`
- Check this page for more information: https://getcomposer.org/doc/00-intro.md

## Cloning the code and setting up the app
1. Clone the repository
2. cd to the repository directory (root of the Symfony app)
3. Run `composer install`
4. On your database server, create a database with name "flexisource_php_exam"
5. Update the Database credential values found in .env file:

    `DATABASE_URL="mysql://root:root@127.0.0.1:3306/flexisource_php_exam?serverVersion=8.0.32&charset=utf8mb4"`
    * change root:root to the username and password you're using for your MySQL database
    * change 127.0.0.1:3306 to the hostname:port you're using for your MySQL database server

6. Generate migrations for this app by executing the command `bin/console make:migration`
7. Run the migrations to create the schema (database tables) by executing the command `bin/console doctrine:migrations:migrate`
8. Execute PHPUnit Tests to make sure all are working correctly by running this command: `./vendor/bin/phpunit`
    
## How to run the Importer as a command
1. Open a terminal and cd to the repository directory (root of the Symfony app)
2. Run the command `bin/console app:customers:import` and follow the prompts.
3. The next couple of questions will ask for the result limit and nationality filter. If you leave each question blank, the app will use the default values.

## Default values for the Randomuser API
1. Default values to be used are editable in config/randomuser_api/importer.yaml
   * default_results_limit - default value to be used as the limit of the results to pull from the randomuser API.
   * nat (under default_filters) - default nationality value to be used to filter the results
   * exc (under default_filters) - default list of fields to be excluded from the results

For more information about the nat and exc filters, please refer to the ff:
https://randomuser.me/documentation#nationalities
https://randomuser.me/documentation#incexc

## Using the Customer API Endpoints

1. Open a terminal and cd to the repository directory (root of the Symfony app)
2. Run the command `symfony serve`

### via any Web Browser
1. GET /customers: Open any browser and type in: `http://127.0.0.1/customers` (assuming symfony serve command gives you 127.0.0.1 as the app URL)
2. GET /customers/{customerId}: Open any browser and type in: `http://127.0.0.1/customers/2` (assuming symfony serve command gives you 127.0.0.1 as the app URL)

### via Postman
1. Create a Postman Collection with 2 requests:
    a. GET /customers, and;
    b. GET /customers/2

## Using the importer on other parts of the application aside from the Command
1. In a controller or a service, include the ff classes:
   use App\Service\RandomUserApiService;
   use App\Service\CustomerService;

2. If used in a non-Controller class, instantiate both classes in the __construct

   private RandomUserApiService $randomUserApi;
   private CustomerService $customer;

        public function __construct(RandomUserApiService $randomUserApi, CustomerService $customer, ParameterBagInterface $params)
        {
            $this->randomUserApi = $randomUserApi;
            $this->customer = $customer;
        }

    Then use the ff methods to import and persist:

        $this->randomUserApi->get($resultLimit, $filterNationality);
        foreach ($importedUsers as $importedUser)
        {
            $this->customer->insert($importedUser);
        }

3. If used in a Controller class, add them as a parameter in any method:

        public function someMethod(
           RandomUserApiService $randomUserApi,
           CustomerService $customerService
        ) {
            // Code here
        }

    Then use the ff methods to import and persist:

        $this->randomUserApi->get($resultLimit, $filterNationality);
        foreach ($importedUsers as $importedUser)
        {
            $this->customer->insert($importedUser);
        }