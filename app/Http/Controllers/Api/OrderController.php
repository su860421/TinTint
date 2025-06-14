<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderIndexRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Contracts\Services\OrderServiceInterface;
use App\Enums\OrderStatusEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {
    }

    public function index(OrderIndexRequest $request): JsonResponse
    {
        try {
            $orders = $this->orderService->index(
                perPage: $request->get('per_page', 20),
                orderBy: $request->get('order_by', 'created_at'),
                orderDirection: $request->get('order_direction', 'desc'),
                relationships: ['orderItems', 'orderItems.product'],
                columns: ['*'],
                filters: []
            );

            return response()->json($orders);
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
            $order = $this->orderService->find($id);

            if (!$order) {
                return response()->json(
                    ['message' => __('messages.orders.not_found')],
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json($order);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return response()->json($order, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request, string $id): JsonResponse
    {
        try {
            $status = OrderStatusEnum::from($request->status);
            $success = $this->orderService->updateOrderStatus($id, $status);

            if (!$success) {
                return response()->json(
                    ['message' => __('messages.orders.not_found')],
                    Response::HTTP_NOT_FOUND
                );
            }

            $order = $this->orderService->find($id);
            return response()->json($order);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->orderService->getOrderStats();
            return response()->json($stats);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
