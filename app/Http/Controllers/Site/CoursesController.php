<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function courses()
    {
        return view('site.courses.courses');
    }
    public function coursesAll()
    {
        return view('site.courses.course-all');
    }
    public function coursesDetail()
    {
        return view('site.courses.course-detail');
    }
}
