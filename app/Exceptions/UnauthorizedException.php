<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UnauthorizedException extends UnauthorizedHttpException
{
    /**
     * @param string     $message   The internal exception message
     * @param \Exception $previous  The previous exception
     * @param int        $code      The internal exception code
     * @param array      $headers
     */
    public function __construct(string $message = null, Exception $previous = null, ?int $code = 0, array $headers = [])
    {
        $challenge = 'Bearer realm=' . config('app.url');

        parent::__construct($challenge, $message ?: 'Unauthorized', $previous, $code, $headers);
    }
}
