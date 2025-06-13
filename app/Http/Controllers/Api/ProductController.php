<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\ProductServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAvailableProducts();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = $this->productService->getProductWithStock($id);
        
        if (!$product) {
            return response()->json(['message' => '商品不存在'], 404);
        }

        return response()->json($product);
    }
}
