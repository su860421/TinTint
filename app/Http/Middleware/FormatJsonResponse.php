<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\ResponseStatus;
use App\Enums\ResponseMessage;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!($response instanceof JsonResponse)) {
            return $response;
        }

        return $this->formatResponse($response);
    }

    private function formatResponse(JsonResponse $response): JsonResponse
    {
        $statusCode = $response->getStatusCode();
        $originalData = $response->original;

        // 如果已經是格式化的響應，直接返回
        if ($this->isFormattedResponse($originalData)) {
            return $response;
        }

        $formattedData = $this->buildFormattedResponse(
            $originalData,
            $statusCode
        );

        return response()->json($formattedData, $statusCode);
    }

    private function isFormattedResponse($data): bool
    {
        return is_array($data) && 
               isset($data['status'], $data['statusCode'], $data['message']);
    }

    private function buildFormattedResponse($data, int $statusCode): array
    {
        $status = ResponseStatus::fromStatusCode($statusCode)->value;
        $message = ResponseMessage::fromStatusCode($statusCode);

        // 處理自定義消息
        if (is_array($data) && isset($data['message'])) {
            $message = $data['message'];
        }

        return [
            'status' => $status,
            'statusCode' => $statusCode,
            'message' => __($message),
            'result' => $this->extractResult($data)
        ];
    }

    private function extractResult($data)
    {
        if ($data instanceof Model) {
            return $data;
        }

        if (isset($data['result'])) {
            return $data['result'];
        }

        return $data;
    }
}
