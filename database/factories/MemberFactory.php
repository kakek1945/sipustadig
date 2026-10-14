<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\MemberType;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([MemberType::Student, MemberType::Teacher]);
        $isStudent = $type === MemberType::Student;

        return [
            'user_id' => null,
            'member_number' => Member::generateMemberNumber(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nisn' => $isStudent ? fake()->numerify('##########') : null,
            'employee_number' => !$isStudent ? fake()->numerify('EMP####') : null,
            'type' => $type,
            'class' => $isStudent ? fake()->randomElement(['X IPA 1', 'X IPA 2', 'XI IPS 1', 'XII IPS 2']) : null,
            'year_level' => $isStudent ? fake()->numberBetween(10, 12) : null,
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'birth_date' => fake()->date(),
            'birth_place' => fake()->city(),
            'gender' => fake()->randomElement([Gender::Male, Gender::Female]),
            'photo' => null,
            'join_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'expired_date' => fake()->dateTimeBetween('now', '+3 years'),
            'status' => 'active',
            'notes' => null,
        ];
    }

    /**
     * Indicate that the member is a student.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MemberType::Student,
        ]);
    }

    /**
     * Indicate that the member is a teacher.
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MemberType::Teacher,
        ]);
    }

    /**
     * Indicate that the member is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the member is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }
}