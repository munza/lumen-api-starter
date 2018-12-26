<?php

namespace App\Transformers;

use Exception;
use Illuminate\Validation\ValidationException;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param  \Exception  $exception
     * @return array
     */
    public function transform(Exception $exception): array
    {
        $data = [
            'error' => [
                'message' => $this->getMessage($exception),
            ],
        ];

        if (count($details = $this->getDetails($exception))) {
            $data['error']['details'] = $details;
        }

        if (config('app.debug')) {
            $data['error']['debug'] = [
                'exception' => class_basename($exception),
                'trace'     => $this->getTrace($exception),
            ];
        }

        return $data;
    }

    /**
     * Get the title of the exception.
     *
     * @param  \Exception  $exception
     * @return string
     */
    private function getMessage(Exception $exception): string
    {
        switch (true) {
            case $exception instanceof NotFoundHttpException:
                return 'Not Found.';

            default:
                return $exception->getMessage();
        }
    }

    /**
     * Get the trace of the exception.
     *
     * @param  \Exception  $exception
     * @return array
     */
    private function getTrace(Exception $exception): array
    {
        return preg_split("/\n/", $exception->getTraceAsString());
    }

    /**
     * Get details of the exception.
     *
     * @param  \Exception  $exception
     * @return array
     */
    private function getDetails(Exception $exception): array
    {
        switch (true) {
            case $exception instanceof ValidationException:
                return $exception->errors();

            default:
                return [];
        }
    }
}
