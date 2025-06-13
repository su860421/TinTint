<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function index(): JsonResponse
    {
        try {
            $products = $this->productService->getAvailableProducts();
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
            $product = $this->productService->getProductWithStock($id);
            
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
