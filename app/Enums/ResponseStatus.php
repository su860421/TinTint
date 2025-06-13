<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum ResponseStatus: string
{
    use InvokableCases;
    use Names;
    use Values;

    case SUCCESS = 'success';
    case ERROR = 'error';
    case NOT_FOUND = 'not found';
    case UNKNOWN = 'unknown status code';

    public static function fromStatusCode(int $statusCode): self
    {
        return match (true) {
            $statusCode >= 200 && $statusCode < 300 => self::SUCCESS,
            $statusCode === 404 => self::NOT_FOUND,
            $statusCode >= 400 => self::ERROR,
            default => self::UNKNOWN,
        };
    }
}
