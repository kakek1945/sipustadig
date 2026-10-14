<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'book_copy_id',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'return_date' => 'date',
        ];
    }

    /**
     * Get the loan this item belongs to.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the book copy.
     */
    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    /**
     * Get the book through copy.
     */
    public function getBook(): ?Book
    {
        return $this->bookCopy?->book;
    }

    /**
     * Check if item is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && $this->due_date->isPast();
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
}
