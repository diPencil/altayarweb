<?php

namespace App\Exceptions;

use Exception;

class IdempotencyConflictException extends Exception
{
    // Custom exception thrown when the same idempotency key is submitted with different payload data
}
