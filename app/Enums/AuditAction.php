<?php

namespace App\Enums;

enum AuditAction: string
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Login = 'login';
    case Logout = 'logout';
    case Borrow = 'borrow';
    case Return = 'return';
    case Extend = 'extend';
    case PayFine = 'pay_fine';
    case WaiveFine = 'waive_fine';
    case Import = 'import';
    case Export = 'export';

    /**
     * Get all audit actions as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get audit action label
     */
    public function label(): string
    {
        return match($this) {
            self::Create => 'Membuat',
            self::Update => 'Mengubah',
            self::Delete => 'Menghapus',
            self::Login => 'Login',
            self::Logout => 'Logout',
            self::Borrow => 'Meminjam',
            self::Return => 'Mengembalikan',
            self::Extend => 'Memperpanjang',
            self::PayFine => 'Membayar Denda',
            self::WaiveFine => 'Menghapuskan Denda',
            self::Import => 'Import',
            self::Export => 'Export',
        };
    }
}