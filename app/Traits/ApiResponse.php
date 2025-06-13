<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = 'Success', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'statusCode' => $code,
            'message' => __($message),
            'result' => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'statusCode' => $code,
            'message' => __($message),
        ], $code);
    }
}
