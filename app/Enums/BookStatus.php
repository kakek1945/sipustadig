<?php

namespace App\Enums;

enum BookStatus: string
{
    case Available = 'available';
    case Borrowed = 'borrowed';
    case Reserved = 'reserved';
    case Lost = 'lost';
    case Damaged = 'damaged';
    case Disposed = 'disposed';

    /**
     * Get all book statuses as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get book status label
     */
    public function label(): string
    {
        return match($this) {
            self::Available => 'Tersedia',
            self::Borrowed => 'Dipinjam',
            self::Reserved => 'Dipesan',
            self::Lost => 'Hilang',
            self::Damaged => 'Rusak',
            self::Disposed => 'Dimusnahkan',
        };
    }

    /**
     * Get book status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::Available => 'green',
            self::Borrowed => 'amber',
            self::Reserved => 'blue',
            self::Lost => 'red',
            self::Damaged => 'orange',
            self::Disposed => 'gray',
        };
    }

    /**
     * Check if book copy can be borrowed
     */
    public function canBorrow(): bool
    {
        return $this === self::Available;
    }
}