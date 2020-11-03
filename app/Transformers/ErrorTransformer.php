<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ErrorTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param  Throwable  $user
     * @return array
     */
    public function transform(Throwable $exception): array
    {
        $error = [
            'message' => (string) $this->parseMessage($exception),
            'status' => (int) $this->parseStatusCode($exception),
        ];

        if (count($details = $this->parseDetails($exception))) {
            $error['details'] = $details;
        }

        if (config('app.debug')) {
            $error['debug'] = [
                'exception' => get_class($exception),
                'trace' => $this->parseTrace($exception),
            ];
        }

        return $error;
    }

    /**
     * Get the message of the exception.
     *
     * @param  Throwable  $exception
     * @return string
     */
    protected function parseMessage(Throwable $exception): string
    {
        if (!config('app.debug') && Response::HTTP_INTERNAL_SERVER_ERROR === $this->parseStatusCode($exception)) {
            return "Something went wrong!";
        }

        switch (true) {
            case $exception instanceof NotFoundHttpException:
                return "Not found";

            // Add custom messages for other exceptions.
        }

        if (method_exists($exception, 'getMessage') && ($message = $exception->getMessage()) != '') {
            return $message;
        }

        return "Something went wrong!";
    }

    /**
     * Get the status code of the exception.
     *
     * @param  Throwable  $exception
     * @return int
     */
    protected function parseStatusCode(Throwable $exception): int
    {
        switch (true) {
            case $exception instanceof ModelNotFoundException:
            case $exception instanceof NotFoundHttpException:
                return Response::HTTP_NOT_FOUND;

            case $exception instanceof ValidationException:
                return Response::HTTP_UNPROCESSABLE_ENTITY;

            // Add custom status code for other exceptions.
        }

        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if (property_exists($exception, 'status')) {
            return $exception->status;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Get the details of the exception.
     *
     * @param  Throwable  $exception
     * @return array
     */
    protected function parseDetails(Throwable $exception): array
    {
        if (method_exists($exception, 'getDetails')) {
            return $exception->getDetails();
        }

        if (method_exists($exception, 'errors')) {
            return $exception->errors();
        }

        return [];
    }

    /**
     * Get the trace of the exception.
     *
     * @param  Throwable  $exception
     * @return array
     */
    protected function parseTrace(Throwable $exception): array
    {
        return preg_split("/\n/", $exception->getTraceAsString());
    }
}
