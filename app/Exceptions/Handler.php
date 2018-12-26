<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception): Response
    {
        if ($this->isJsonRequest($request)) {
            $response = [
                'message' => (string) $exception->getMessage(),
                'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];

            switch (true) {
                case $exception instanceof HttpException:
                    $response['message'] = Response::$statusTexts[$exception->getStatusCode()];
                    $response['status']  = $exception->getStatusCode();
                    break;

                case $exception instanceof ModelNotFoundException:
                    $response['message'] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
                    $response['status']  = Response::HTTP_NOT_FOUND;
                    break;

                case $exception instanceof ValidationException:
                    $response['message'] = Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY];
                    $response['status']  = Response::HTTP_UNPROCESSABLE_ENTITY;
                    $response['details'] = $exception->errors();
                    break;
            }

            if ($this->isDebugMode()) {
                $response['debug'] = [
                    'exception' => get_class($exception),
                    'trace'     => preg_split("/\n/", $exception->getTraceAsString()),
                ];
            }

            return response()->json(['error' => $response], $response['status']);
        }

        return parent::render($request, $exception);
    }

    /**
     * Determine if the request is a JSON request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isJsonRequest(Request $request): bool
    {
        return true;
    }

    /**
     * Determine if the debug mode is enabled.
     *
     * @return bool
     */
    private function isDebugMode(): bool
    {
        return (bool) env('APP_DEBUG');
    }
}
