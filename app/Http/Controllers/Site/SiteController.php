<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        return view('site.index');
    }
    public function about()
    {
        return view('site.about.about');
    }
    public function aboutDetail()
    {
        return view('site.about.about-detail');
    }
    public function computerClasses()
    {
        return view('site.category.computer-classes');
    }
    public function languageClasses()
    {
        return view('site.category.language-classes');
    }
    public function otherClasses()
    {
        return view('site.category.other-classes');
    }
}
