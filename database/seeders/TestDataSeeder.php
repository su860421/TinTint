<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;

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

        $this->command->info('開始生成用戶數據...');
        $users = $this->generateUsers();

        $this->command->info('開始生成商品數據...');
        $products = $this->generateProducts();

        $this->command->info('開始生成訂單數據...');
        $this->generateOrders($users, $products);

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

    private function generateUsers(): array
    {
        $users = [];
        for ($i = 1; $i <= 200; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $name = $firstName . $lastName;

            $users[] = User::create([
                'name' => $name,
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }
        $this->command->info('已生成 200 個用戶');
        return $users;
    }

    private function generateProducts(): array
    {
        $products = [];

        // 生成預定義商品
        foreach ($this->predefinedProducts as [$name, $price, $stock]) {
            $products[] = Product::create([
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
            ]);
        }

        // 生成其他商品到達100個
        for ($i = count($this->predefinedProducts) + 1; $i <= 100; $i++) {
            $category = $this->categories[array_rand($this->categories)];
            $products[] = Product::create([
                'name' => "{$category}商品-{$i}",
                'price' => rand(100, 10000),
                'stock' => rand(50, 500),
            ]);
        }

        $this->command->info('已生成 100 個商品');
        return $products;
    }

    private function generateOrders(array $users, array $products): void
    {
        for ($i = 1; $i <= 1000; $i++) {
            $user = $users[array_rand($users)];
            $itemsCount = rand(1, 3);
            $status = $this->getWeightedStatus();
            $orderDate = $this->generateOrderDate($i);

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'status' => $status->value,
                'total_amount' => 0,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // 選擇不重複的商品
            $selectedProducts = collect($products)->random($itemsCount);
            $totalAmount = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $subtotal = $product->price * $quantity;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            if ($i % 100 === 0) {
                $this->command->info("已生成 {$i} 筆訂單");
            }
        }

        $this->command->info('已生成 1000 筆訂單和對應明細');
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
        if ($orderIndex > 950 && rand(1, 100) <= 30) {
            $hour = rand(8, 22);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            return now()->format('Y-m-d') . " {$hour}:{$minute}:{$second}";
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

        $totalOrders = Order::count();
        $this->command->info("總訂單數: {$totalOrders}");

        $totalItems = OrderItem::count();
        $this->command->info("總明細數: {$totalItems}");

        $this->command->info('訂單狀態分佈:');
        $statusCounts = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        foreach ($statusCounts as $status) {
            $this->command->info("  {$status->status->label()}: {$status->count}");
        }

        $today = Order::whereDate('created_at', now());
        $todayCount = $today->count();
        $todayAmount = $today->sum('total_amount');
        $this->command->info("今天訂單: {$todayCount} 筆, 金額: $" . number_format($todayAmount));

        $totalAmount = Order::sum('total_amount');
        $this->command->info("訂單總金額: $" . number_format($totalAmount));

        $this->command->info("\n測試資料生成完成！可以開始測試 API 了。");
    }
}
