<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MemberType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_number',
        'name',
        'email',
        'nisn',
        'employee_number',
        'type',
        'class',
        'year_level',
        'phone',
        'address',
        'birth_date',
        'birth_place',
        'gender',
        'photo',
        'join_date',
        'expired_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => MemberType::class,
            'gender' => Gender::class,
            'birth_date' => 'date',
            'join_date' => 'date',
            'expired_date' => 'date',
        ];
    }

    /**
     * Get the user associated with this member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get loans for this member.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get fines for this member.
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Get active loans count.
     */
    public function activeLoansCount(): int
    {
        return $this->loans()->where('status', 'active')->count();
    }

    /**
     * Get overdue loans count.
     */
    public function overdueLoansCount(): int
    {
        return $this->loans()->where('status', 'overdue')->count();
    }

    /**
     * Get unpaid fines total.
     */
    public function unpaidFinesTotal(): float
    {
        return $this->fines()
            ->where('status', 'pending')
            ->sum('amount');
    }

    /**
     * Check if member can borrow.
     */
    public function canBorrow(): bool
    {
        // Check if blocked
        if ($this->status === 'blocked') {
            return false;
        }

        // Check if expired
        if ($this->expired_date && $this->expired_date->isPast()) {
            return false;
        }

        // Check unpaid fines threshold
        if ($this->unpaidFinesTotal() > 50000) {
            return false;
        }

        return true;
    }

    /**
     * Generate member number.
     */
    public static function generateMemberNumber(): string
    {
        $year = date('Y');
        $lastMember = static::whereYear('join_date', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMember && preg_match('/(\d+)$/', $lastMember->member_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('MBR-%s-%04d', $year, $sequence);
    }
}