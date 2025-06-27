<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Exceptions;

use Exception;

class RepositoryException extends Exception
{
    protected $code = 500;

    public function __construct(string $message = '', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
