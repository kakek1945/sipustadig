<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'sort_order',
    ];

    /**
     * Get books in this category.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get books count.
     */
    public function booksCount(): int
    {
        return $this->books()->count();
    }

    /**
     * Get total copies in this category.
     */
    public function totalCopies(): int
    {
        return $this->books()
            ->with('copies')
            ->get()
            ->sum(fn($book) => $book->copies->count());
    }
}