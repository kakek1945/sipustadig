<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Active = 'active';
    case Returned = 'returned';
    case Overdue = 'overdue';
    case Extended = 'extended';

    /**
     * Get all loan statuses as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get loan status label
     */
    public function label(): string
    {
        return match($this) {
            self::Active => 'Aktif',
            self::Returned => 'Dikembalikan',
            self::Overdue => 'Terlambat',
            self::Extended => 'Diperpanjang',
        };
    }

    /**
     * Get loan status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::Active => 'green',
            self::Returned => 'gray',
            self::Overdue => 'red',
            self::Extended => 'blue',
        };
    }
}