<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => fake()->isbn13(),
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'publish_year' => fake()->year(),
            'edition' => fake()->randomElement([null, '1st Edition', '2nd Edition', 'Revised']),
            'description' => fake()->paragraph(),
            'cover_image' => null,
            'language' => 'Indonesian',
            'page_count' => fake()->numberBetween(50, 500),
            'dimensions' => fake()->randomElement(['14 x 21 cm', '15 x 23 cm', '17 x 24 cm']),
            'weight' => fake()->randomFloat(2, 0.1, 1.0),
            'category_id' => null,
            'rack_location' => 'R-' . fake()->randomLetter() . '-' . fake()->numberBetween(1, 20),
            'price' => fake()->randomFloat(2, 10000, 200000),
            'notes' => null,
            'is_digital' => false,
            'digital_source' => null,
            'digital_url' => null,
            'digital_format' => null,
            'digital_grade_level' => null,
            'digital_access_note' => null,
        ];
    }

    /**
     * Create a book without ISBN.
     */
    public function withoutIsbn(): static
    {
        return $this->state(fn (array $attributes) => [
            'isbn' => null,
        ]);
    }

    /**
     * Create a book with a specific category.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    public function digital(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_digital' => true,
            'digital_source' => 'SIBI/Kemendikdasmen',
            'digital_url' => 'https://buku.kemendikdasmen.go.id/katalog/' . fake()->slug(),
            'digital_format' => 'PDF',
            'digital_grade_level' => 'Umum',
            'digital_access_note' => 'Buka melalui situs resmi.',
            'rack_location' => null,
        ]);
    }
}