<?php

namespace App\Enums;

enum MemberType: string
{
    case Student = 'student';
    case Teacher = 'teacher';

    /**
     * Get all member types as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get member type label
     */
    public function label(): string
    {
        return match($this) {
            self::Student => 'Siswa',
            self::Teacher => 'Guru',
        };
    }
}