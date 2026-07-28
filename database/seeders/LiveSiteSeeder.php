<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;

class LiveSiteSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

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
