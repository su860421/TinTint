<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductIndexRequest;
use App\Contracts\Services\ProductServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    public function index(ProductIndexRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->index(
                perPage: $request->get('per_page', 20),
                orderBy: $request->get('order_by', 'created_at'),
                orderDirection: $request->get('order_direction', 'desc'),
                relationships: [],
                columns: ['*'],
                filters: $request->get('filters', [])
            );
            
            return response()->json($products);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $product = $this->productService->find($id);
            
            if (!$product) {
                return response()->json(
                    ['message' => __('messages.products.not_found')],
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json($product);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
