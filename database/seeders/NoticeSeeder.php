<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'title' => 'Dashain Special TOEFL Class',
                'image' => 'dashain-special-toefl-class.png',
                'status' => 'active',
            ],
            [
                'title' => 'Dashain Special PTE Class',
                'image' => 'dashain-special-pte-class.png',
                'status' => 'active',
            ],
            [
                'title' => 'Dashain Special Ielts Class',
                'image' => 'dashain-special-ielts-class.png',
                'status' => 'active',
            ],
            [
                'title' => 'Dasain Special Web Development Class',
                'image' => 'dasain-special-webdevelopment-class.png',
                'status' => 'active',
            ],
        ];
        \App\Models\Notice::insert($data);
    }
}
