<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function courses()
    {
        $data= [
            'courses' => Courses::get()->take(3)
        ];
        return view('site.courses.courses', $data);
    }
    public function coursesAll()
    {
        $data= [
            'courses' => Courses::all()
        ];
        return view('site.courses.course-all', $data);
    }
    public function coursesDetail($slug)
    {
        $data=[
            'course' => Courses::where('slug',$slug)->first()
        ];
        return view('site.courses.course-detail',$data);
    }
    public function courseCatagory($slug)
    {
        $data=[
            'courses' => Courses::where('category_slug',$slug)->get()
        ];
        return view('site.courses.courses-catagory',$data);
    }
}
