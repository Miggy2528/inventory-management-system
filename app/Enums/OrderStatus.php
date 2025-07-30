<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case COMPLETE = 'complete';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::COMPLETE => __('Complete'),
            self::CANCELLED => __('Cancelled'),
        };
    }
}
