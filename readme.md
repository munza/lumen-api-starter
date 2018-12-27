# Lumen API Starter

A starter project to develop API with Lumen 5.7 (***Updated***).

This project has been rewritten from scratch. If you are looking for the previous version which was made for Lumen v5.4 please use this [link](https://github.com/munza/lumen-api-starter/tree/v5.4) or navigate to branch `v5.4`.

### Included Packages
- [spatie/laravel-fractal@^5.4](https://github.com/spatie/laravel-fractal)
- [tymon/jwt-auth@1.0.0-rc.3](https://github.com/tymondesigns/jwt-auth)
- [spatie/laravel-query-builder@^1.12](https://github.com/spatie/laravel-query-builder)
- [flipbox/lumen-generator@^5.6](https://github.com/flipboxstudio/lumen-generator)

### Installation

- Clone the Repo:
    - `git clone git@github.com:munza/lumen-api-starter.git`
    - `git clone https://github.com/munza/lumen-api-starter.git`
- `cd lumen-api-starter`
- `composer create-project`
- `php artisan key:generate`
- `php artisan jwt:secret`
- `php artisan migrate`
- `php artisan serve`

#### Create new user

- `php artisan ti`
- `factory('App\Models\User')->create(['email' => 'admin@localtest.me', 'password' => 'secret'])`

### Configuration

- Edit `.env` file for database connection configuration.
- Edit the files located under `config` directory for configuration.

### Usage

#### Adding a new resource endpoint

- Add endpoint in `routes/web.php`.

    ```php
    $router->group(['middleware' => 'auth:api'], function ($router) {
        $app->get('/users', 'UserController@index');
    });
    ```

- Add controller with `php artisan make:controller {name}` command

- Add model at `php artisan make:model {name}`. You can use `-m` flag to add migration file and `-f` flag for factory file.

- Add service at `app` directory.

    ```php
    <?php

    namespace App;

    class Accounts
    {
        // Add service methods.
    }
    ```

- Load the service in controller.

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

- Add transformers at `app/Transformers` directory or use the command `php artisan make:transformer {name}`.

    ```php
    <?php

    namespace App\Transformers;

    user App\User;
    use League\Fractal\TransformerAbstract;

    class UserTransformer extends TransformerAbstract
    {
        /**
         * Transform object to array.
         *
         * @param  \App\User $user
            * @return array
            */
        public function transform(User $user)
        {
            return [
                'id' => (int) $user->id,
                'email' => (string) $user->email,
            ];
        }
    }
    ```

- Render JSON in controllers

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

- Customize exception response at `app/Traits/ExceptionRenderable.php` file.

    ```php
    <?php

    namespace App\Traits;

    // use declarations...

    trait ExceptionRenderable
    {
        /**
        * Error transformer.
        *
        * @param  \Exception  $exception
        *
        * @return array
        */
        public function transform(Exception $exception): array
        {
            $error = $this->defaultErrorResponse($exception);

            switch (true) {
                case $exception instanceof ModelNotFoundException:
                    $error['status'] = Response::HTTP_NOT_FOUND;
                    break;

                case $exception instanceof NotFoundHttpException:
                    $error['message'] = 'Not found';
                    break;

                case $exception instanceof ValidationException:
                    $error['details'] = $exception->errors();
                    break;

                    // Add more exceptions here...
            }

            // remaining codes
        }
    }
    ```

### Todo

- [ ] Move all the extended features inside a package.

### Issues

Please create an issue if you find any bug or error.

### Contribution

Feel free to make a pull request if you want to add anything.

### License

MIT
