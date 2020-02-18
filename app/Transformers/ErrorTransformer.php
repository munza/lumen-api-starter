<?php

namespace App\Transformers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param  \Exception  $user
     *
     * @return array
     */
    public function transform(Exception $exception): array
    {
        $error = [
            'message' => (string) $this->getMessage($exception),
            'status' => (string) $this->getStatusCode($exception),
        ];

        if (count($details = $this->getDetails($exception))) {
            $error['details'] = $details;
        }

        if (config('app.debug')) {
            if (config('app.debug')) {
                $error['debug'] = [
                    'exception' => class_basename($exception),
                    'trace' => $this->getTrace($exception),
                ];
            }
        }

        return $error;
    }

    /**
     * Get the message of the exception.
     *
     * @param  \Exception  $exception
     *
     * @return string
     */
    protected function getMessage(Exception $exception): string
    {
        if (method_exists($exception, 'getMessage') and ($message = $exception->getMessage()) != '') {
            return $message;
        }

        switch (true) {
            case $exception instanceof NotFoundHttpException:
                return "Not found";
        }

        return "Unknown error";
    }

    /**
     * Get the status code of the exception.
     *
     * @param  \Exception  $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if (property_exists($exception, 'status')) {
            return $exception->status;
        }

        switch (true) {
            case $exception instanceof ModelNotFoundException:
            case $exception instanceof NotFoundHttpException:
                return Response::HTTP_NOT_FOUND;

            case $exception instanceof ValidationException:
                return Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Get the details of the exception.
     *
     * @param  \Exception  $exception
     *
     * @return array
     */
    protected function getDetails(Exception $exception): array
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
     * @param  \Exception  $exception
     *
     * @return array
     */
    protected function getTrace(Exception $exception): array
    {
        return preg_split("/\n/", $exception->getTraceAsString());
    }
}
