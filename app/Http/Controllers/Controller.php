<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return error response
     */
    protected function errorResponse($errors, int $code = 400, string $message = 'Error')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return validation error response
     */
    protected function validationError($errors)
    {
        return $this->errorResponse($errors, 422, 'Validation failed');
    }

    /**
     * Return not found response
     */
    protected function notFound($message = 'Resource not found')
    {
        return $this->errorResponse($message, 404, 'Not found');
    }

    /**
     * Return unauthorized response
     */
    protected function unauthorized($message = 'Unauthorized')
    {
        return $this->errorResponse($message, 401, 'Unauthorized');
    }
}
