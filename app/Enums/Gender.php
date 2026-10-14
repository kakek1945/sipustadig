<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    /**
     * Get all genders as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get gender label
     */
    public function label(): string
    {
        return match($this) {
            self::Male => 'Laki-laki',
            self::Female => 'Perempuan',
        };
    }
}