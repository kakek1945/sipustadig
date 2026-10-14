<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'publish_year',
        'edition',
        'description',
        'cover_image',
        'language',
        'page_count',
        'dimensions',
        'weight',
        'category_id',
        'rack_location',
        'price',
        'notes',
        'is_digital',
        'digital_source',
        'digital_url',
        'digital_format',
        'digital_grade_level',
        'digital_access_note',
    ];

    protected function casts(): array
    {
        return [
            'page_count' => 'integer',
            'weight' => 'decimal:2',
            'price' => 'decimal:2',
            'is_digital' => 'boolean',
        ];
    }

    /**
     * Get the category of this book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get copies of this book.
     */
    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    /**
     * Get loans through copies.
     */
    public function loans(): HasManyThrough
    {
        return $this->hasManyThrough(Loan::class, BookCopy::class);
    }

    /**
     * Get total copies count.
     */
    public function totalCopies(): int
    {
        return $this->copies()->count();
    }

    /**
     * Get available copies count.
     */
    public function availableCopies(): int
    {
        return $this->copies()->where('status', 'available')->count();
    }

    /**
     * Get borrowed copies count.
     */
    public function borrowedCopies(): int
    {
        return $this->copies()->where('status', 'borrowed')->count();
    }

    /**
     * Get loan count.
     */
    public function loanCount(): int
    {
        return $this->loans()->count();
    }

    /**
     * Check if book has available copies.
     */
    public function hasAvailableCopies(): bool
    {
        return $this->availableCopies() > 0;
    }
}