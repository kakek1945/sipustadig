<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('is_digital')->default(false)->after('notes');
            $table->string('digital_source')->nullable()->after('is_digital');
            $table->string('digital_url')->nullable()->after('digital_source');
            $table->string('digital_format')->nullable()->after('digital_url');
            $table->string('digital_grade_level')->nullable()->after('digital_format');
            $table->text('digital_access_note')->nullable()->after('digital_grade_level');

            $table->index('is_digital');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['is_digital']);
            $table->dropColumn([
                'is_digital',
                'digital_source',
                'digital_url',
                'digital_format',
                'digital_grade_level',
                'digital_access_note',
            ]);
        });
    }
};
