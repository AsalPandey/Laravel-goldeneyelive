<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Basic Computer Class',
                'slug' => 'basic-computer-class',
                'description' => 'Learn Laravel 8 from scratch',
                'course_outline' => 'Laravel 8 course outline/curriculum/structure/syllabus',
                'photo' => 'course-1.jpg',
                'price' => '100',
                'rating_star' => '4.5',
                'rating_count' => '100',
                'capacity' => '10',
                'duration' => '3 months',
                'instructor' => 'John Doe',
                'category' => 'computer classes',
                'catagory_slug' => 'computer-classes',
            ],
            [
                'name' => 'Vue 3',
                'slug' => 'vue-3',
                'description' => 'Learn Vue 3 from scratch',
                'course_outline' => 'Vue 3 course outline',
                'photo' => 'course-3.jpg',
                'price' => '50',
                'rating_star' => '4.0',
                'rating_count' => '50',
                'capacity' => '5',
                'duration' => '2 months',
                'instructor' => 'Jane Doe',
                'category' => 'language classes',
                'catagory_slug' => 'language-classes',
            ],
            [
                'name' => 'React',
                'slug' => 'react',
                'description' => 'Learn React from scratch',
                'course_outline' => 'React course outline',
                'photo' => 'course-4.jpg',
                'price' => '75',
                'capacity' => '8',
                'rating_star' => '4.2',
                'rating_count' => '75',
                'duration' => '2.5 months',
                'instructor' => 'John Doe',
                'category' => 'other classes',
                'catagory_slug' => 'other-classes',
            ],
        ];
        \App\Models\Courses::insert($data);
    }
}
