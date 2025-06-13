<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\Uid\Ulid;

trait HasUlid
{
    public static function bootHasUlid(): void
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) new Ulid();
            }
        });
    }

    public function initializeHasUlid(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }
}
