<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'copy_code',
        'barcode',
        'qr_code',
        'status',
        'condition',
        'acquisition_date',
        'purchase_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookStatus::class,
            'acquisition_date' => 'date',
            'purchase_price' => 'decimal:2',
        ];
    }

    /**
     * Get the book this copy belongs to.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get loan items for this copy.
     */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Get active loan for this copy.
     */
    public function activeLoan()
    {
        return $this->loanItems()
            ->whereIn('status', ['borrowed'])
            ->whereHas('loan', fn($q) => $q->where('status', 'active'))
            ->first();
    }

    /**
     * Check if copy is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === BookStatus::Available;
    }

    /**
     * Check if copy can be borrowed.
     */
    public function canBorrow(): bool
    {
        return $this->status->canBorrow() && !$this->activeLoan();
    }

    /**
     * Generate copy code.
     */
    public static function generateCopyCode(Book $book): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $book->title), 0, 3));
        $lastCopy = static::where('book_id', $book->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCopy && preg_match('/(\d+)$/', $lastCopy->copy_code, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%03d', $prefix, date('ymd'), $sequence);
    }
}