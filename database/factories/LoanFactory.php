<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loanDate = fake()->dateTimeBetween('-1 month', 'now');
        $dueDate = (clone $loanDate)->modify('+7 days');

        return [
            'loan_number' => Loan::generateLoanNumber(),
            'member_id' => Member::factory(),
            'processed_by' => User::factory()->librarian(),
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'status' => LoanStatus::Active,
            'extension_count' => 0,
            'notes' => null,
        ];
    }

    /**
     * Indicate that the loan is returned.
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::Returned,
            'return_date' => fake()->dateTimeBetween($attributes['loan_date'] ?? 'now', 'now'),
        ]);
    }

    /**
     * Indicate that the loan is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::Overdue,
            'due_date' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
        ]);
    }

    /**
     * Indicate that the loan has been extended.
     */
    public function extended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanStatus::Extended,
            'extension_count' => 1,
        ]);
    }
}