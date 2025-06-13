<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum ResponseMessage: string
{
    use InvokableCases;
    use Names;
    use Values;

    case GET_SUCCESS = 'Resource get successfully';
    case CREATE_SUCCESS = 'Resource created successfully';
    case UPDATE_SUCCESS = 'Resource updated successfully';
    case DELETE_SUCCESS = 'Resource deleted successfully';
    case BAD_REQUEST = 'Bad request';
    case UNAUTHORIZED = 'Unauthorized';
    case FORBIDDEN = 'Forbidden';
    case NOT_FOUND = 'Resource not found';
    case SERVER_ERROR = 'Server error';
    case UNKNOWN_ERROR = 'Unknown error';

    public static function fromStatusCode(int $statusCode): string
    {
        return match ($statusCode) {
            200 => self::GET_SUCCESS->value,
            201 => self::CREATE_SUCCESS->value,
            204 => self::DELETE_SUCCESS->value,
            400 => self::BAD_REQUEST->value,
            401 => self::UNAUTHORIZED->value,
            403 => self::FORBIDDEN->value,
            404 => self::NOT_FOUND->value,
            500 => self::SERVER_ERROR->value,
            default => self::UNKNOWN_ERROR->value,
        };
    }
}
