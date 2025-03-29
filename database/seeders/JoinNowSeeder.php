<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JoinNowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'johndoe@example.com',
                'phone' => '1234567890',
                'address' => '123 Main Street',
                'course' => 'Computer Science',
                'queries' => 'What is the course duration?'

            ]
            ];
        \App\Models\JoinNowQueries::insert($data);
    }
}
