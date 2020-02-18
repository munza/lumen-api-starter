<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\ErrorHandler\Error\FatalError;

trait ExceptionRenderable
{
    /**
     * Response the exception in JSON.
     *
     * @param \Exception $exception
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponse(Exception $exception, TransformerAbstract $transformer, SerializerAbstract $serializer): JsonResponse
    {
        $error = fractal($exception, new $transformer(), new $serializer)->toArray();

        return response()->json($error)
            ->setStatusCode($this->getStatusCode($error))
            ->withHeaders($this->getHeaders($exception));
    }

    /**
     * Check if the exception is renderable with JSON
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     *
     * @return bool
     */
    public function isJsonRenderable($request, Exception $exception): bool
    {
        if (config('app.debug') && $this->isFatalError($exception)) {
            return false;
        }

        return true;
    }

    /**
     * Get the status code of the exception.
     *
     * @param  array  $error
     *
     * @return int
     */
    public function getStatusCode(array $error): int
    {
        if ($status = Arr::get($error, 'data.status')) {
            return $status;
        }

        if ($status = Arr::get($error, 'error.status')) {
            return $status;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Get the headers of the exception.
     *
     * @param  \Exception  $exception
     *
     * @return array
     */
    private function getHeaders(Exception $exception): array
    {
        if (method_exists($exception, 'getHeaders')) {
            return call_user_func([$exception, 'getHeaders']);
        }

        return [];
    }

    private function isFatalError(Exception $exception): bool
    {
        return $exception instanceof FatalError || $exception instanceof FatalThrowableError;
    }
}
