<?php

namespace Database\Factories;

use App\Enums\FineStatus;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    protected $model = Fine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lateDays = fake()->numberBetween(1, 30);
        $dailyRate = 1000;
        $amount = $lateDays * $dailyRate;

        return [
            'member_id' => Member::factory(),
            'loan_id' => Loan::factory()->returned(),
            'loan_item_id' => null,
            'processed_by' => User::factory()->librarian(),
            'fine_number' => Fine::generateFineNumber(),
            'late_days' => $lateDays,
            'amount' => $amount,
            'paid_amount' => 0,
            'status' => FineStatus::Pending,
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
            'paid_date' => null,
            'waiver_reason' => null,
            'notes' => null,
        ];
    }

    /**
     * Indicate that the fine is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FineStatus::Paid,
            'paid_amount' => $attributes['amount'],
            'paid_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the fine is waived.
     */
    public function waived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FineStatus::Waived,
            'waiver_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the fine is partially paid.
     */
    public function partiallyPaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_amount' => $attributes['amount'] / 2,
        ]);
    }
}