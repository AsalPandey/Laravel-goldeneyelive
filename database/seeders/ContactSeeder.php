<?php

namespace Database\Seeders;

use App;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            [
                'name' => 'John Doe',
                'phone' => '1234567890',
                'email' => 'abc@gmail.com',
                'subject' => 'Test Subject',
                'message' => 'Test Message',
            ]
        ];
        \App\Models\Contact::insert($data);
    }
}
