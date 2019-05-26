# Lumen API Starter

A starter project to develop API with Lumen 5.7 (**_Updated_**).

This project has been rewritten from scratch. If you are looking for the previous version which was made for Lumen v5.4 please use this [link](https://github.com/munza/lumen-api-starter/tree/v5.4) or navigate to branch `v5.4`.

### Included Packages

-   [spatie/laravel-fractal@^5.4](https://github.com/spatie/laravel-fractal)
-   [tymon/jwt-auth@1.0.0-rc.3](https://github.com/tymondesigns/jwt-auth)
-   [spatie/laravel-query-builder@^1.12](https://github.com/spatie/laravel-query-builder)
-   [flipbox/lumen-generator@^5.6](https://github.com/flipboxstudio/lumen-generator)

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
-   `factory('App\Models\User')->create(['email' => 'admin@localtest.me', 'password' => 'secret'])`

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

### Todo

-   [x] Remove the customization feature from error trait.
-   [ ] Move all the extended features inside a package.
-   [ ] Add the feature to use a transformer for error response.

### Issues

Please create an issue if you find any bug or error.

### Contribution

Feel free to make a pull request if you want to add anything.

### License

MIT
