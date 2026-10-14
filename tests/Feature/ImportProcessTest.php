<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_access_import_page_and_download_template(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)
            ->get(route('import.index'))
            ->assertStatus(200)
            ->assertSee('Import Data');

        $this->actingAs($librarian)
            ->get(route('import.template', 'members'))
            ->assertStatus(200);
    }

    public function test_librarian_can_import_members_csv(): void
    {
        $librarian = User::factory()->librarian()->create();
        $file = UploadedFile::fake()->createWithContent('members.csv', implode("\n", [
            'name,email,type,nisn,employee_number,class,year_level,phone,address,gender',
            'Sari,sari@example.test,student,2234567890,,XI IPS 1,11,0813,Jalan,female',
        ]));

        $this->actingAs($librarian)
            ->post(route('import.process'), [
                'type' => 'members',
                'file' => $file,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('members', ['email' => 'sari@example.test']);
    }

    public function test_student_cannot_access_import_page(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('import.index'))
            ->assertStatus(403);
    }
}
