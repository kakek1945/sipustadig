<?php

namespace Database\Factories;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookCopy>
 */
class BookCopyFactory extends Factory
{
    protected $model = BookCopy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'copy_code' => function (array $attributes) {
                static $sequence = 1;
                $book = Book::find($attributes['book_id']) ?? Book::factory()->create();
                $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $book->title), 0, 3));
                return sprintf('%s-%s-%03d-%04d', $prefix, date('ymd'), $sequence++, rand(1000, 9999));
            },
            'barcode' => fake()->numerify('BC###########'),
            'qr_code' => null,
            'status' => BookStatus::Available,
            'condition' => 'good',
            'acquisition_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'purchase_price' => fake()->randomFloat(2, 10000, 200000),
            'notes' => null,
        ];
    }

    /**
     * Indicate that the copy is borrowed.
     */
    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatus::Borrowed,
        ]);
    }

    /**
     * Indicate that the copy is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatus::Available,
        ]);
    }

    /**
     * Indicate that the copy is lost.
     */
    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatus::Lost,
        ]);
    }

    /**
     * Indicate that the copy is damaged.
     */
    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatus::Damaged,
        ]);
    }
}