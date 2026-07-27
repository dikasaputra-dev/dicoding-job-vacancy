<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete()
                ->restrictOnDelete();

            $table->string('title', 150)->index();
            $table->string('position', 100);
            $table->string('employment_type', 30);
            $table->unsignedSmallInteger('candidate_count');

            $table->date('expires_at')->index();

            $table->string('location', 120);
            $table->boolean('is_remote')->default(false);

            $table->longText('description');

            $table->unsignedBigInteger('salary_min');
            $table->unsignedBigInteger('salary_max')->nullable();
            $table->boolean('show_salary')->default(false);

            $table->string('minimum_experience', 30);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
