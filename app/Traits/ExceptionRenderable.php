<?php

namespace App\Traits;

use App\Transformers\ErrorTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Component\Debug\Exception\FatalThrowableError;

trait ExceptionRenderable
{
    /**
     * Render JSON exception.
     *
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderJsonException(Exception $exception): JsonResponse
    {
        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $data = new Item($exception, new ErrorTransformer());

        return response()->json(
            $manager->createData($data)->toArray()
        )->setStatusCode($this->getStatusFromException($exception));
    }

    /**
     * Check if the exception is renderable with JSON
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return bool
     */
    private function isJsonRenderable(Request $request, Exception $exception): bool
    {
        if (config('app.debug') && $exception instanceof FatalThrowableError) {
            return false;
        }

        return true;
    }

    /**
     * Get status code from exception.
     *
     * @param  \Exception  $exception
     * @return int
     */
    private function getStatusFromException(Exception $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if (property_exists($exception, 'status')) {
            return $exception->status;
        }

        // Add remaining exceptions in the switch-case.
        switch (true) {
            case $exception instanceof ModelNotFoundException:
                return Response::HTTP_NOT_FOUND;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;

    }
}
