# Lumen API Starter

A starter template to develop API with Lumen 8.

### Included Packages

-   [flipbox/lumen-generator@^7.0](https://github.com/flipboxstudio/lumen-generator)
-   [fruitcake/laravel-cors@^2.0](https://github.com/fruitcake/laravel-cors)
-   [spatie/laravel-fractal@^5.7](https://github.com/spatie/laravel-fractal)
-   [spatie/laravel-query-builder@^2.8](https://github.com/spatie/laravel-query-builder)
-   [tymon/jwt-auth@^1.0](https://github.com/tymondesigns/jwt-auth)

### Installation

-   Clone the Repo:
    -   `git clone git@github.com:munza/lumen-api-starter.git`
    -   `git clone https://github.com/munza/lumen-api-starter.git`
-   `cd lumen-api-starter`
-   `composer create-project`
-   `php artisan key:generate`
-   `php artisan jwt:secret`
-   `php artisan migrate`
-   `php artisan serve`

#### Create new user

-   `php artisan ti`
-   `factory('App\Models\User')->create(['email' => 'admin@localtest.me', 'password' => 'password'])`

### Configuration

-   Edit `.env` file for database connection configuration.
-   Edit the files located under `config` directory for configuration.

### Usage

#### Adding a new resource endpoint

-   Add endpoint in `routes/web.php`.

    ```php
    $router->group(['middleware' => 'auth:api'], function ($router) {
        $app->get('/users', 'UserController@index');
    });
    ```

-   Add controller with `php artisan make:controller {name}` command

-   Add model at `php artisan make:model {name}`. You can use `-m` flag to add migration file and `-f` flag for factory file.

-   Add service at `app` directory.

    ```php
    <?php

    namespace App;

    class Accounts
    {
        // Add service methods.
    }
    ```

-   Load the service in controller.

    ```php
    <?php

    namespace App\Http\Controllers;

    use App\Accounts;

    class UserController extends Controller
    {
        /**
         * Controller constructor.
         *
         * @param  \App\Accounts  $accounts
         */
        public function __construct(Accounts $accounts)
        {
            $this->accounts = $accounts;
        }

        // Add controller methods.
    }
    ```

    You can also use Facade for the services. But I do not prefer to use Facade in Lumen personally.

-   Add transformers at `app/Transformers` directory or use the command `php artisan make:transformer {name}`.

    ```php
    <?php

    namespace App\Transformers;

    use App\User;
    use League\Fractal\TransformerAbstract;

    class UserTransformer extends TransformerAbstract
    {
        /**
         * Transform object to array.
         *
         * @param  \App\User $user
         * @return array
         */
        public function transform(User $user): array
        {
            return [
                'id' => (int) $user->id,
                'email' => (string) $user->email,
            ];
        }
    }
    ```

-   Render JSON in controllers

    ```php
    <?php

    namespace App\Http\Controllers;

    use App\Accounts;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;

    class UserController extends Controller
    {
        /**
         * Controller constructor.
         *
         * @param  \App\Accounts  $accounts
         */
        public function __construct(Accounts $accounts)
        {
            $this->accounts = $accounts;
        }

        /**
         * List of all users.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function index(): JsonResponse
        {
            $users = $this->accounts->getUsersWithPagination($request);

            return response()->json($users, Response::HTTP_OK);
        }
    }
    ```

-   Exception message, status code and details can be displayed by declaring these as methods in an exception class.

    ```php
    <?php

    namespace App\Exceptions;

    use Symfony\Component\HttpKernel\Exception\HttpException;

    class CustomException extends HttpException
    {
        public function getMessage(): string
        {
            return 'Custom message';
        }

        public function getStatusCode(): int
        {
            return 500;
        }

        public function getDetails(): ?array
        {
            return [];
        }
    }
    ```

#### Using CORS

Please check [fruitcake/laravel-cors](https://github.com/fruitcake/laravel-cors) in Github for the usage details.

#### Using Docker

Docker can be run with `docker-compose up -d` command. After that, any command stated above can be executed with Docker. The format is — `docker-compose app exec <command>` or `docker exec lumen-app <command>`. For example database can be migrated with `docker-compose app exec php artisan migrate`.

_I would suggest not to use Docker Compose while deploying in production._

### Todo

-   [ ] Move all the extended features inside a package.

### Issues

Please create an issue if you find any bug or error.

### Contribution

Feel free to make a pull request if you want to add anything.

### License

MIT
