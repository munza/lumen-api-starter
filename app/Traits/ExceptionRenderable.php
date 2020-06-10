<?php

namespace App\Traits;

use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use Throwable;

trait ExceptionRenderable
{
    /**
     * Response the exception in JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderJson(Request $request, Throwable $exception, ?TransformerAbstract $transformer = null, ?SerializerAbstract $serializer = null): JsonResponse
    {
        $error = fractal(
            $exception,
            $transformer ?: new $this->transformer,
            $serializer ?: new $this->serializer,
        )->toArray();

        return response()->json($error)
            ->setStatusCode($this->getStatusCode($error))
            ->withHeaders($this->getHeaders($exception));
    }

    /**
     * Check if the exception is renderable with JSON
     *
     * @param  Throwable  $exception
     *
     * @return bool
     */
    private function isJsonRenderable(Throwable $exception): bool
    {
        if (config('app.debug') && $exception instanceof Error) {
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
    private function getStatusCode(array $error): int
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
     * @param  Throwable  $exception
     *
     * @return array
     */
    private function getHeaders(Throwable $exception): array
    {
        if (method_exists($exception, 'getHeaders')) {
            return call_user_func([$exception, 'getHeaders']);
        }

        return [];
    }
}
