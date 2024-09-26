<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $data= [
            'courses' => Courses::get()->take(3)
        ];
        return view('site.index', $data);
    }
    public function about()
    {
        return view('site.about.about');
    }
    public function aboutDetail()
    {
        return view('site.about.about-detail');
    }
}
