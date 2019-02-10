<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                $error['status'] = $error['status'] ?? Response::HTTP_NOT_FOUND;
                break;

            case $exception instanceof NotFoundHttpException:
                isset($error['message']) && !$error['message'] ?: $error['message'] = 'Not found';
                break;

            case $exception instanceof ValidationException:
                $error['details'] = $error['details'] ?? $exception->errors();
                break;
        }

        // Add exception trace for debug.
        if (config('app.debug')) {
            $error['debug'] = [
                'exception' => class_basename($exception),
                'trace' => $this->getTrace($exception),
            ];
        }

        return $error;
    }

    /**
     * Build a default error response body.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    private function defaultErrorResponse(Exception $exception): array
    {
        $error = [];

        // Set the message from the exception.
        $error['message'] = $exception->getMessage() ?: 'Unknown error';

        // Set the status code from the exception.
        switch (true) {

            case method_exists($exception, 'getStatusCode'):
                $error['status'] = $exception->getStatusCode();
                break;

            case property_exists($exception, 'status'):
                $error['status'] = $exception->status;
                break;

            default:
                $error['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        // Set the error details from the exception.
        switch (true) {
            case method_exists($exception, 'getDetails'):
                $error['details'] = $exception->getDetails();
                break;

            case method_exists($exception, 'errors'):
                $error['details'] = $exception->errors();
                break;
        }

        return $error;
    }

    /**
     * Get the trace of the exception.
     *
     * @param  \Exception  $exception
     *
     * @return array
     */
    private function getTrace(Exception $exception): array
    {
        return preg_split("/\n/", $exception->getTraceAsString());
    }

    /**
     * Check if the exception is renderable with JSON
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     *
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
     * Render JSON exception.
     *
     * @param  \Exception  $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderJsonException(Exception $exception): JsonResponse
    {
        $error = $this->transform($exception);

        return response()->json(compact('error'))
            ->setStatusCode($error['status'])
            ->withHeaders($this->getHeaders($exception));
    }

    /**
     * Get the headers of the exception.
     *
     * @param  \Exception  $exception
     *
     * @return array
     */
    public function getHeaders(Exception $exception): array
    {
        if (method_exists($exception, 'getHeaders')) {
            return $exception->getHeaders();
        }

        return [];
    }
}
