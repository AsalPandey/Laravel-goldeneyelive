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
                'title' => 'Web Development Class',
                'image' => 'web-development-class.png',
                'status' => 'active',
            ],
        ];
        \App\Models\Notice::insert($data);
    }
}
