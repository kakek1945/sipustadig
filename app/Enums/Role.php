<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Librarian = 'librarian';
    case Principal = 'principal';
    case Teacher = 'teacher';
    case Student = 'student';

    /**
     * Get all roles as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role label for display
     */
    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::Librarian => 'Pustakawan',
            self::Principal => 'Kepala Sekolah',
            self::Teacher => 'Guru',
            self::Student => 'Siswa',
        };
    }

    /**
     * Get permissions for role
     */
    public function permissions(): array
    {
        return match($this) {
            self::Admin => [
                'manage_users',
                'manage_members',
                'manage_books',
                'manage_copies',
                'manage_loans',
                'manage_returns',
                'manage_fines',
                'view_reports',
                'manage_settings',
                'view_audit_logs',
                'import_data',
            ],
            self::Librarian => [
                'manage_members',
                'manage_books',
                'manage_copies',
                'manage_loans',
                'manage_returns',
                'manage_fines',
                'view_reports',
                'view_audit_logs',
                'import_data',
            ],
            self::Principal => [
                'view_reports',
                'view_dashboard',
            ],
            self::Teacher => [
                'borrow_books',
                'return_books',
                'extend_loans',
                'view_catalog',
            ],
            self::Student => [
                'borrow_books',
                'return_books',
                'extend_loans',
                'view_catalog',
            ],
        };
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    /**
     * Check if role is staff (admin or librarian)
     */
    public function isStaff(): bool
    {
        return in_array($this, [self::Admin, self::Librarian]);
    }

    /**
     * Check if role can manage others
     */
    public function canManage(): bool
    {
        return $this->isStaff();
    }
}