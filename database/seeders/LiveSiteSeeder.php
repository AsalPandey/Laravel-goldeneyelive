<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LiveSiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CourseCategorySeeder::class,
            CourseSeeder::class,
            NoticeSeeder::class,
            FAQSeeder::class,
            BlogSeeder::class,
            SiteSettingSeeder::class,
            ServicePillarSeeder::class,
            TeacherSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
