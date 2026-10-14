<?php

namespace App\Enums;

enum FineStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Waived = 'waived';

    /**
     * Get all fine statuses as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get fine status label
     */
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Belum Lunas',
            self::Paid => 'Lunas',
            self::Waived => 'Dihapuskan',
        };
    }

    /**
     * Get fine status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::Pending => 'red',
            self::Paid => 'green',
            self::Waived => 'gray',
        };
    }
}