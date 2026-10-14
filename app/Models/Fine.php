<?php

namespace App\Models;

use App\Enums\FineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'loan_id',
        'loan_item_id',
        'processed_by',
        'fine_number',
        'late_days',
        'amount',
        'paid_amount',
        'status',
        'due_date',
        'paid_date',
        'waiver_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'late_days' => 'integer',
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'status' => FineStatus::class,
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    /**
     * Get the member this fine belongs to.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the loan this fine belongs to.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the loan item this fine belongs to.
     */
    public function loanItem(): BelongsTo
    {
        return $this->belongsTo(LoanItem::class);
    }

    /**
     * Get the user who processed this fine.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get remaining amount to pay.
     */
    public function remainingAmount(): float
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    /**
     * Check if fine is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->status === FineStatus::Paid;
    }

    /**
     * Generate fine number.
     */
    public static function generateFineNumber(): string
    {
        $prefix = 'FNE' . date('ymd');
        $lastFine = static::where('fine_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastFine && preg_match('/(\d+)$/', $lastFine->fine_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%04d', $prefix, $sequence);
    }
}