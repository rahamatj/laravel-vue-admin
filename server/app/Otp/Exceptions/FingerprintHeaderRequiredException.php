<?php

namespace App\Otp\Exceptions;

use Exception;

class FingerprintHeaderRequiredException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage()
        ], 422);
    }
}
