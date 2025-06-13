<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Contracts\Services\OrderServiceInterface;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var OrderServiceInterface
     */
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * 取得訂單列表（支援分頁）
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $orders = $this->orderService->getPaginated($perPage);
        return response()->json($orders);
    }

    /**
     * 取得單一訂單詳情
     */
    public function show($id)
    {
        $order = $this->orderService->getOrderWithDetails($id);

        if (!$order) {
            return response()->json(['message' => '訂單不存在'], 404);
        }

        return response()->json($order);
    }

    /**
     * 建立新訂單
     */
    public function store(CreateOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 更新訂單狀態
     */
    public function updateStatus(UpdateOrderStatusRequest $request, $id)
    {
        try {
            $status = OrderStatusEnum::from($request->status);
            $success = $this->orderService->updateOrderStatus($id, $status);

            if (!$success) {
                return response()->json(['message' => '訂單不存在'], 404);
            }

            $order = $this->orderService->getOrderWithDetails($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 取得訂單統計資料
     */
    public function stats()
    {
        $stats = $this->orderService->getOrderStats();
        return response()->json($stats);
    }
}
