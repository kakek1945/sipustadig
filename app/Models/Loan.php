<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number',
        'member_id',
        'processed_by',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'extension_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'status' => LoanStatus::class,
            'extension_count' => 'integer',
        ];
    }

    /**
     * Get the member who borrowed.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who processed this loan.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get loan items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Get fines for this loan.
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Check if loan is overdue.
     */
    public function isOverdue(): bool
    {
        return in_array($this->status, [LoanStatus::Active, LoanStatus::Extended, LoanStatus::Overdue], true)
            && $this->due_date->isPast();
    }

    /**
     * Get days overdue.
     */
    public function daysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return (int) $this->due_date->diffInDays(now());
    }

    /**
     * Check if loan can be extended.
     */
    public function canExtend(): bool
    {
        // Can only extend active loans
        if ($this->status !== LoanStatus::Active && $this->status !== LoanStatus::Extended) {
            return false;
        }

        // Check extension limit (e.g., max 2 extensions)
        $settings = app('settings');
        $maxExtensions = $settings->get('loan.max_extensions', 2);

        return $this->extension_count < $maxExtensions;
    }

    /**
     * Get total items.
     */
    public function totalItems(): int
    {
        return $this->items()->count();
    }

    /**
     * Get returned items count.
     */
    public function returnedItemsCount(): int
    {
        return $this->items()->where('status', 'returned')->count();
    }

    /**
     * Generate loan number.
     */
    public static function generateLoanNumber(): string
    {
        $prefix = 'LN' . date('ymd');
        $lastLoan = static::where('loan_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLoan && preg_match('/(\d+)$/', $lastLoan->loan_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%04d', $prefix, $sequence);
    }
}
