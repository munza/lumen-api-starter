<?php

namespace App\Exceptions;

use App\Serializers\ErrorSerializer;
use App\Traits\ExceptionRenderable;
use App\Transformers\ErrorTransformer;
use Error;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ExceptionRenderable;

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
     * The transformer class for the error.
     *
     * @var string
     */
    protected $transformer = ErrorTransformer::class;

    /**
     * The serializer class for the error.
     *
     * @var string
     */
    protected $serializer = ErrorSerializer::class;

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable                $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($this->checkIfJsonRenderable($exception)) {
            return $this->renderJson($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
