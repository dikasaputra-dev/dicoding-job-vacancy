<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::query()->updateOrCreate(
            [
                'slug' => 'dicoding-indonesia',
            ],
            [
                'name' => 'Dicoding Indonesia',
                'logo_path' => '/images/companies/dicoding-logo.svg',
                'business_sector' => 'Technology',
                'employee_size' => '51-200',
                'headquarters_location' => 'Bandung',
                'website_url' => 'https://www.dicoding.com',
            ],
        );
    }
}
