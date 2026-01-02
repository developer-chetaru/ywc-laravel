<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait ApiResponseTrait
{
    /**
     * Return a standardized success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a standardized error response
     */
    protected function errorResponse(string $message, int $statusCode = 400, array $errors = null): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a standardized validation error response
     */
    protected function validationErrorResponse(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $exception->errors(),
        ], 422);
    }

    /**
     * Return a standardized unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return a standardized not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }
}

