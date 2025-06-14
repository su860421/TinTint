<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Uid\Ulid;

class TestDataSeeder extends Seeder
{
    private array $predefinedProducts = [
        ['iPhone 15 Pro', 35900, 150],
        ['Samsung Galaxy S24', 28900, 200],
        ['MacBook Pro 14吋', 68900, 80],
        ['Dell XPS 13', 45900, 120],
        ['Sony WH-1000XM5 耳機', 9900, 300],
        ['AirPods Pro', 7490, 250],
        ['Nike Air Max 90', 3200, 180],
        ['Adidas Ultraboost 22', 5400, 160],
        ['Uniqlo 純棉T恤', 590, 500],
        ['Zara 休閒外套', 1990, 220],
        ['H&M 牛仔褲', 1290, 180],
        ['阿里山高山茶', 1200, 300],
        ['藍山咖啡豆', 800, 150],
        ['鐵觀音茶葉', 650, 280],
        ['哥倫比亞咖啡豆', 720, 200],
        ['SK-II 神仙水', 8900, 100],
        ['蘭蔻小黑瓶', 3200, 150],
        ['資生堂洗髮精', 650, 300],
        ['Oral-B 電動牙刷', 2890, 120],
        ['Chanel No.5 香水', 4200, 80],
        ['羅技 MX Master 3 滑鼠', 2990, 200],
        ['Cherry MX 機械鍵盤', 4500, 100],
        ['LG 27吋 4K 顯示器', 12900, 60],
        ['SanDisk 256GB 隨身碟', 890, 400],
        ['Anker 20000mAh 行動電源', 1590, 250]
    ];

    private array $categories = [
        '手機',
        '筆電',
        '耳機',
        '服飾',
        '咖啡',
        '茶葉',
        '護膚品',
        '3C周邊',
        '食品',
        '生活用品'
    ];

    private array $firstNames = [
        '張',
        '李',
        '王',
        '陳',
        '林',
        '黃',
        '周',
        '吳',
        '徐',
        '朱',
        '馬',
        '胡',
        '郭',
        '何',
        '高'
    ];

    private array $lastNames = [
        '志明',
        '美華',
        '小明',
        '雅婷',
        '建國',
        '淑芬',
        '俊傑',
        '麗華',
        '文雄',
        '秀英',
        '偉強',
        '玉蘭',
        '明智',
        '惠美',
        '國強'
    ];

    private array $statusWeights = [
        OrderStatusEnum::PENDING->value => 0.1,
        OrderStatusEnum::PROCESSING->value => 0.15,
        OrderStatusEnum::COMPLETED->value => 0.7,
        OrderStatusEnum::CANCELLED->value => 0.05,
    ];

    public function run(): void
    {
        $this->command->info('開始清空資料...');
        $this->truncateTables();

        // 1. 生成用戶數據
        $this->command->info('開始生成用戶數據...');
        $userUlids = $this->generateUlids(200);
        $userCount = 0;
        foreach (array_chunk($userUlids, 50) as $batchUlids) {
            $userData = [];
            foreach ($batchUlids as $ulid) {
                $userCount++;
                $firstName = $this->firstNames[array_rand($this->firstNames)];
                $lastName = $this->lastNames[array_rand($this->lastNames)];
                $userData[] = [
                    'id' => $ulid,
                    'name' => $firstName . $lastName,
                    'email' => "user{$userCount}@example.com",
                    'password' => bcrypt('password'),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            DB::table('users')->insert($userData);
        }
        $this->command->info('已生成200個用戶');

        // 2. 生成商品數據
        $this->command->info('開始生成商品數據...');
        $productUlids = $this->generateUlids(100);
        $productData = [];

        foreach ($this->predefinedProducts as $index => [$name, $price, $stock]) {
            $productData[] = [
                'id' => $productUlids[$index],
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        for ($i = count($this->predefinedProducts); $i < 100; $i++) {
            $category = $this->categories[array_rand($this->categories)];
            $productData[] = [
                'id' => $productUlids[$i],
                'name' => "{$category}商品-{$i}",
                'price' => rand(100, 10000),
                'stock' => rand(50, 500),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('products')->insert($productData);
        $this->command->info('已生成100個商品');

        // 3. 關閉模型事件
        Order::unsetEventDispatcher();
        OrderItem::unsetEventDispatcher();

        // 4. 獲取用戶和商品數據
        $users = DB::table('users')->select('id')->get();
        $products = DB::table('products')->select('id', 'price')->get();
        $userIds = $users->pluck('id')->toArray();

        // 5. 分批生成訂單
        $this->command->info('開始生成訂單數據...');
        $totalOrders = 1000000;
        $batchSize = 1000;
        $startTime = now();
        $orderIndex = 0;  // 添加全局订单索引

        for ($i = 0; $i < $totalOrders; $i += $batchSize) {
            $orderUlids = $this->generateUlids($batchSize);
            $orders = [];
            $orderItems = [];
            $currentBatch = min($batchSize, $totalOrders - $i);

            for ($j = 0; $j < $currentBatch; $j++) {
                $orderIndex++;  // 递增订单索引
                $itemsCount = rand(1, 3);
                $selectedProducts = $products->random($itemsCount);
                $totalAmount = 0;
                $orderItemUlids = $this->generateUlids($itemsCount);
                $orderDate = $this->generateOrderDate($i + $j);

                foreach ($selectedProducts as $idx => $product) {
                    $quantity = rand(1, 3);
                    $subtotal = $product->price * $quantity;
                    $totalAmount += $subtotal;

                    $orderItems[] = [
                        'id' => $orderItemUlids[$idx],
                        'order_id' => $orderUlids[$j],
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal
                    ];
                }

                $orders[] = [
                    'id' => $orderUlids[$j],
                    'user_id' => $userIds[array_rand($userIds)],
                    'order_number' => 'ORD' . str_pad($orderIndex, 6, '0', STR_PAD_LEFT),
                    'status' => $this->getWeightedStatus()->value,
                    'total_amount' => $totalAmount,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ];
            }

            DB::table('orders')->insert($orders);
            DB::table('order_items')->insert($orderItems);

            if (($i + $batchSize) % 10000 === 0) {
                $progress = ($i + $batchSize) / $totalOrders * 100;
                $timeElapsed = now()->diffInSeconds($startTime);
                $estimatedTotal = ($timeElapsed / ($i + $batchSize)) * $totalOrders;
                $timeRemaining = $estimatedTotal - $timeElapsed;

                $this->command->info(sprintf(
                    '進度: %.2f%% (已生成 %d 筆訂單) - 預計剩餘時間: %d 分 %d 秒',
                    $progress,
                    $i + $batchSize,
                    floor($timeRemaining / 60),
                    $timeRemaining % 60
                ));
            }
        }

        $this->showStatistics();
    }

    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function generateUlids(int $count): array
    {
        $ulids = [];
        for ($i = 0; $i < $count; $i++) {
            $ulids[] = (string) new Ulid();
        }
        return $ulids;
    }

    private function getWeightedStatus(): OrderStatusEnum
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;

        foreach ($this->statusWeights as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return OrderStatusEnum::from($status);
            }
        }

        return OrderStatusEnum::PENDING;
    }

    private function generateOrderDate(int $orderIndex): string
    {
        // 最後50筆訂單中，30%是今天的
        if ($orderIndex > 999950 && rand(1, 100) <= 30) {
            return now()
                ->setHour(rand(8, 22))
                ->setMinute(rand(0, 59))
                ->setSecond(rand(0, 59))
                ->format('Y-m-d H:i:s');
        }

        // 其他訂單在過去1年內
        return now()
            ->subDays(rand(1, 365))
            ->setHour(rand(8, 22))
            ->setMinute(rand(0, 59))
            ->setSecond(rand(0, 59))
            ->format('Y-m-d H:i:s');
    }

    private function showStatistics(): void
    {
        $this->command->info('=== 測試資料統計 ===');

        $totalOrders = DB::table('orders')->count();
        $this->command->info("總訂單數: {$totalOrders}");

        $totalItems = DB::table('order_items')->count();
        $this->command->info("總明細數: {$totalItems}");

        $this->command->info('訂單狀態分佈:');
        $statusCounts = DB::table('orders')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($statusCounts as $status) {
            $statusEnum = OrderStatusEnum::from($status->status);
            $this->command->info("  {$statusEnum->label()}: {$status->count}");
        }

        $today = DB::table('orders')->whereDate('created_at', now());
        $todayCount = $today->count();
        $todayAmount = $today->sum('total_amount');
        $this->command->info("今天訂單: {$todayCount} 筆, 金額: $" . number_format($todayAmount, 2));

        $totalAmount = DB::table('orders')->sum('total_amount');
        $this->command->info("訂單總金額: $" . number_format($totalAmount, 2));

        $this->command->info("\n測試資料生成完成！可以開始測試 API 了。");
    }
}
