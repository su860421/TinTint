<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum OrderStatusEnum: string
{
    use InvokableCases;
    use Names;
    use Values;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => '待處理',
            self::PROCESSING => '處理中',
            self::COMPLETED => '已完成',
            self::CANCELLED => '已取消',
        };
    }

    public static function defaultStatus(): self
    {
        return self::PENDING;
    }
}
